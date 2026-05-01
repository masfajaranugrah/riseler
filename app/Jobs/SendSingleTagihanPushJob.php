<?php

namespace App\Jobs;

use App\Models\Tagihan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendSingleTagihanPushJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 12;
    public int $timeout = 20;

    public function __construct(public string $tagihanId)
    {
    }

    public function handle(): void
    {
        $tagihan = Tagihan::with('pelanggan')
            ->where('id', $this->tagihanId)
            ->where('status_pembayaran', 'belum bayar')
            ->first();

        if (!$tagihan) {
            Log::info('SendSingleTagihanPushJob skipped: tagihan not found/already paid', [
                'tagihan_id' => $this->tagihanId,
            ]);
            return;
        }

        $pelanggan = $tagihan->pelanggan;
        if (!$pelanggan || empty($pelanggan->webpushr_sid)) {
            Log::info('SendSingleTagihanPushJob skipped: sid empty', [
                'tagihan_id' => $tagihan->id,
                'pelanggan_id' => $pelanggan?->id,
            ]);
            return;
        }

        $result = $this->sendWebpushrNotification([
            'title' => 'Pemberitahuan untuk Anda',
            'message' => "Halo {$pelanggan->nama_lengkap}, kami baru saja menerbitkan tagihan untuk Anda. Silakan cek detailnya.",
            'target_url' => url('/dashboard/customer/tagihan'),
            'sid' => $pelanggan->webpushr_sid,
        ]);

        if ($result['success']) {
            Log::info('SendSingleTagihanPushJob success', [
                'tagihan_id' => $tagihan->id,
                'pelanggan_id' => $pelanggan->id,
            ]);
            return;
        }

        if (!empty($result['rate_limited'])) {
            $delay = $this->nextDelaySeconds();
            Log::warning('SendSingleTagihanPushJob rate_limited, will retry', [
                'tagihan_id' => $tagihan->id,
                'pelanggan_id' => $pelanggan->id,
                'attempt' => $this->attempts(),
                'retry_in_seconds' => $delay,
                'http_code' => $result['http_code'] ?? null,
                'response' => $result['response'] ?? null,
            ]);
            $this->release($delay);
            return;
        }

        Log::error('SendSingleTagihanPushJob failed', [
            'tagihan_id' => $tagihan->id,
            'pelanggan_id' => $pelanggan->id,
            'http_code' => $result['http_code'] ?? null,
            'error' => $result['error'] ?? null,
            'response' => $result['response'] ?? null,
        ]);
    }

    private function nextDelaySeconds(): int
    {
        $schedule = [60, 120, 300, 600, 900, 1200, 1800, 3600];
        $index = min(max($this->attempts() - 1, 0), count($schedule) - 1);
        return $schedule[$index];
    }

    /**
     * @param array{title:string,message:string,target_url:string,sid:string} $data
     * @return array{success:bool,rate_limited?:bool,http_code?:int,error?:string,response?:array<string,mixed>|null}
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
                CURLOPT_TIMEOUT => 15,
                CURLOPT_CONNECTTIMEOUT => 5,
            ]);

            $responseRaw = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            $response = is_string($responseRaw) ? json_decode($responseRaw, true) : null;
            $type = strtolower((string) ($response['type'] ?? ''));
            $description = strtolower((string) ($response['description'] ?? ''));

            $isRateLimited = $httpCode === 429
                || $type === 'rate_limit'
                || str_contains($description, 'too many requests')
                || str_contains($description, 'rate limit');

            if ($httpCode === 200 && !empty($responseRaw) && !$isRateLimited) {
                return ['success' => true, 'http_code' => $httpCode, 'response' => $response];
            }

            return [
                'success' => false,
                'rate_limited' => $isRateLimited,
                'http_code' => $httpCode,
                'error' => $curlError ?: null,
                'response' => $response,
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
