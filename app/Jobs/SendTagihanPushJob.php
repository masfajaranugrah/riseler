<?php

namespace App\Jobs;

use App\Models\Tagihan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendTagihanPushJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 0; // allow long running
    public $tries = 0; // unlimited — WithoutOverlapping releases count as attempts
    public $maxExceptions = 3; // hanya gagal jika ada 3 exception asli

    /**
     * @param array<int|string> $tagihanIds
     */
    public function __construct(public array $tagihanIds)
    {
        // Pastikan masuk ke queue default tanpa mendefinisikan properti duplikat
        $this->onQueue('default');
    }

    public function handle(): void
    {
        $sent = 0;
        $ignored = 0;
        $failed = 0;
        $batchCounter = 0;

        // --- Konfigurasi batch & delay ---
        // Kirim 400 pelanggan per batch
        $batchSize = max(1, (int) env('WEBPUSHR_BATCH_SIZE', 400));
        // Jeda 4 detik per pelanggan
        $perRequestDelaySec = max(1, (int) env('WEBPUSHR_DELAY_SECONDS', 4));
        // Jeda 30 menit antar batch
        $batchPauseMinutes = max(1, (int) env('WEBPUSHR_BATCH_PAUSE_MINUTES', 20));

        Log::info('SendTagihanPushJob started', [
            'total_ids'           => count($this->tagihanIds),
            'batch_size'          => $batchSize,
            'delay_per_request_s' => $perRequestDelaySec,
            'batch_pause_min'     => $batchPauseMinutes,
        ]);

        // Ambil semua tagihan yang valid sekaligus (sudah di-filter SID-nya)
        $tagihans = Tagihan::query()
            ->whereIn('id', $this->tagihanIds)
            ->where('status_pembayaran', 'belum bayar')
            ->whereHas('pelanggan', function ($query): void {
                $query->whereNotNull('webpushr_sid')
                    ->where('webpushr_sid', '!=', '');
            })
            ->with(['pelanggan:id,nama_lengkap,webpushr_sid'])
            ->get();

        $totalToProcess = $tagihans->count();
        Log::info("SendTagihanPushJob: found {$totalToProcess} tagihan with valid SID");

        foreach ($tagihans as $tagihan) {
            try {
                $pelanggan = $tagihan->pelanggan;
                $sid = trim((string) ($pelanggan?->webpushr_sid ?? ''));

                if (!$pelanggan || $sid === '') {
                    $ignored++;
                    Log::info('SendTagihanPushJob ignored: missing sid', [
                        'tagihan_id'   => $tagihan->id,
                        'pelanggan_id' => $tagihan->pelanggan_id,
                    ]);
                    continue;
                }

                // ---- Kirim notifikasi ----
                $result = $this->sendWebpushrNotification([
                    'title'      => 'Tagihan Belum Dibayar',
                    'message'    => "Halo {$pelanggan->nama_lengkap}, tagihan Anda akan jatuh tempo pada " . ($tagihan->tanggal_berakhir ?? 'segera') . '. Mohon segera lakukan pembayaran.',
                    'target_url' => url('/dashboard/customer/tagihan'),
                    'sid'        => $sid,
                ]);

                if ($result['success']) {
                    $sent++;
                    Log::info("SendTagihanPushJob: sent #{$sent} to {$pelanggan->nama_lengkap} (tagihan #{$tagihan->id})");
                } elseif (!empty($result['rate_limited'])) {
                    $failed++;
                    Log::warning('SendTagihanPushJob rate limited by provider — waiting extra 60s', [
                        'tagihan_id' => $tagihan->id,
                        'http_code'  => $result['http_code'] ?? null,
                    ]);
                    // Jika kena rate limit dari provider, tunggu 60 detik lalu lanjut
                    sleep(60);
                } else {
                    $failed++;
                    Log::error('SendTagihanPushJob failed send', [
                        'tagihan_id' => $tagihan->id,
                        'http_code'  => $result['http_code'] ?? null,
                        'error'      => $result['error'] ?? null,
                    ]);
                }

                // Progress log setiap 50 pelanggan
                if (($batchCounter + 1) % 50 === 0) {
                    Log::info("SendTagihanPushJob progress: " . ($batchCounter + 1) . "/{$totalToProcess} processed (sent={$sent}, failed={$failed}, ignored={$ignored})");
                }

                $batchCounter++;

                // ---- Jeda 4 detik per pelanggan ----
                sleep($perRequestDelaySec);

                // ---- Jeda 30 menit setiap 400 pelanggan ----
                if ($batchCounter % $batchSize === 0) {
                    $batchNumber = $batchCounter / $batchSize;
                    $pauseSeconds = $batchPauseMinutes * 60;

                    Log::info("SendTagihanPushJob: batch #{$batchNumber} selesai ({$batchCounter}/{$totalToProcess}). Jeda {$batchPauseMinutes} menit...");

                    sleep($pauseSeconds);

                    $nextBatch = $batchNumber + 1;
                    Log::info("SendTagihanPushJob: jeda selesai, melanjutkan batch #{$nextBatch}...");
                }

            } catch (\Throwable $e) {
                $failed++;
                Log::error('SendTagihanPushJob error per item', [
                    'tagihan_id' => $tagihan->id,
                    'error'      => $e->getMessage(),
                ]);
                // Tetap jeda meskipun error, agar tidak burst
                sleep($perRequestDelaySec);
                $batchCounter++;
            }
        }

        Log::info('SendTagihanPushJob finished', [
            'sent'    => $sent,
            'ignored' => $ignored,
            'failed'  => $failed,
            'total_processed' => $batchCounter,
        ]);
    }

    public function failed(Throwable $e): void
    {
        Log::error('SendTagihanPushJob failed permanently', [
            'error' => $e->getMessage(),
            'tagihan_ids_count' => count($this->tagihanIds),
        ]);
    }



    /**
     * Kirim push ke Webpushr.
     *
     * @param array{title:string,message:string,target_url:string,sid:string} $data
     * @return array{success:bool,error?:string,http_code?:int,response?:string|false,rate_limited?:bool}
     */
    private function sendWebpushrNotification(array $data): array
    {
        try {
            $ch = curl_init('https://api.webpushr.com/v1/notification/send/sid');

            $payload = [
                'title' => $data['title'],
                'message' => $data['message'],
                'target_url' => $data['target_url'],
                'sid' => $data['sid'],
            ];

            // Pakai fallback agar tetap kirim jika env belum di-set (menyamai controller lama)
            $headers = [
                'Content-Type: application/json',
                'webpushrKey: ' . env('WEBPUSHR_KEY', '2ee12b373a17d9ba5f44683cb42d4279'),
                'webpushrAuthToken: ' . env('WEBPUSHR_TOKEN', '116294'),
            ];

            curl_setopt_array($ch, [
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_CONNECTTIMEOUT => 5,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            $parsed = is_string($response) ? json_decode($response, true) : null;
            $type = strtolower((string) ($parsed['type'] ?? ''));
            $description = strtolower((string) ($parsed['description'] ?? ''));

            $rateLimited = in_array($httpCode, [420, 429], true)
                || $type === 'rate_limit'
                || str_contains($description, 'too many requests')
                || str_contains($description, 'rate limit');

            if ($httpCode === 200 && $response !== false && !$rateLimited) {
                return ['success' => true, 'response' => $response];
            }

            return [
                'success' => false,
                'http_code' => $httpCode,
                'response' => $response,
                'error' => $curlError ?: null,
                'rate_limited' => $rateLimited,
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
