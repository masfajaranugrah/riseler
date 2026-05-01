<?php

namespace App\Jobs;

use App\Models\Iklan;
use App\Models\Pelanggan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendIklanPushJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 0;     // jangan dimatiin walau lama
    public $tries = 3;       // retry kalau gagal

    public function __construct(public string $iklanId) {}

    public function handle()
    {
        $iklan = Iklan::find($this->iklanId);

        if (!$iklan) {
            Log::warning('Iklan tidak ditemukan', ['iklan_id' => $this->iklanId]);
            return;
        }

        Log::info('Mulai kirim push iklan', ['iklan_id' => $iklan->id]);

        $sentCount = 0;
        $failedCount = 0;

        // PENTING: pakai cursor supaya aman 10.000+
        $pelanggans = Pelanggan::whereNotNull('webpushr_sid')
            ->where('webpushr_sid', '!=', '')
            ->cursor();

        foreach ($pelanggans as $pelanggan) {
            try {
                $result = $this->sendWebpushrNotification([
                    'title' => $iklan->title,
                    'message' => $iklan->message,
                    'target_url' => url('https://layanan.jernih.net.id/dashboard/customer/tagihan/home'),
                    'sid' => $pelanggan->webpushr_sid,
                ]);

                if ($result['success']) {
                    $sentCount++;
                } else {
                    $failedCount++;
                }

                // throttle ringan biar API & server adem
                usleep(200000); // 0.2 detik

            } catch (\Throwable $e) {
                $failedCount++;
                Log::error('Gagal kirim push', [
                    'pelanggan_id' => $pelanggan->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $iklan->update([
            'total_sent' => $sentCount,
            'status' => 'sent',
            'sent_at' => now()
        ]);

        Log::info('Selesai kirim push iklan', [
            'iklan_id' => $iklan->id,
            'sent' => $sentCount,
            'failed' => $failedCount
        ]);
    }

    /**
     * Kirim push ke Webpushr
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
                'webpushrKey: ' . env('WEBPUSHR_KEY'),
                'webpushrAuthToken: ' . env('WEBPUSHR_TOKEN'),
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
            curl_close($ch);

            return ($httpCode === 200)
                ? ['success' => true]
                : ['success' => false, 'http_code' => $httpCode];

        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
