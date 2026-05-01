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

class SendWebPushrJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $iklanId)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $iklan = Iklan::find($this->iklanId);
        
        if (!$iklan) {
            Log::warning('SendWebPushrJob: iklan not found', ['iklan_id' => $this->iklanId]);
            return;
        }

        try {
            $pelanggans = Pelanggan::whereNotNull('webpushr_sid')
                ->where('webpushr_sid', '!=', '')
                ->get();

            if ($pelanggans->isEmpty()) {
                Log::info('Tidak ada pelanggan dengan webpushr_sid');
                return;
            }

            $sentCount = 0;
            $failedCount = 0;

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
                        sleep(2); // Jeda 2 detik per user
                    } else {
                        $failedCount++;
                    }

                } catch (\Exception $e) {
                    $failedCount++;
                    Log::error('Error sending to ' . $pelanggan->nama_lengkap, ['error' => $e->getMessage()]);
                    continue;
                }
            }

            // Update total sent
            $iklan->update(['total_sent' => $sentCount]);

            Log::info('Push notification summary', [
                'sent' => $sentCount,
                'failed' => $failedCount,
                'total' => $pelanggans->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Push notification error: ' . $e->getMessage());
        }
    }

    private function sendWebpushrNotification($data)
    {
        try {
            $ch = curl_init('https://api.webpushr.com/v1/notification/send/sid');

            $payload = [
                'title' => $data['title'] ?? 'Notifikasi',
                'message' => $data['message'] ?? '',
                'target_url' => $data['target_url'] ?? url('/'),
                'sid' => $data['sid'],
            ];

            $headers = [
                'Content-Type: application/json',
                'webpushrKey: ' . env('WEBPUSHR_KEY', '2ee12b373a17d9ba5f44683cb42d4279'),
                'webpushrAuthToken: ' . env('WEBPUSHR_TOKEN', '116294'),
            ];

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            $responseData = json_decode($response, true);

            if ($httpCode == 200 && !empty($response)) {
                return ['success' => true, 'response' => $responseData];
            } else {
                return ['success' => false, 'error' => $curlError ?: 'HTTP Code: ' . $httpCode];
            }

        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
