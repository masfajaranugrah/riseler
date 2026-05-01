<?php

namespace App\Jobs;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\Pelanggan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class BroadcastAdminChatJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 172800; // 2 hari

    public function __construct(
        public string $broadcastId,
        public string $adminId,
        public string $messageText,
        public int $totalPelanggan
    ) {
    }

    public function handle(): void
    {
        $cacheKey = "broadcast_admin_{$this->broadcastId}";
        $done = 0;

        Log::info('BroadcastAdminChat started', [
            'broadcast_id' => $this->broadcastId,
            'admin_id' => $this->adminId,
            'total' => $this->totalPelanggan,
            'queue' => $this->queue,
        ]);

        try {
            Pelanggan::select('id', 'webpushr_sid')->chunkById(200, function ($pelanggans) use (&$done, $cacheKey) {
                foreach ($pelanggans as $pelanggan) {
                    $message = Message::create([
                        'sender_id' => $this->adminId,
                        'receiver_id' => $pelanggan->id,
                        'chat_type' => 'admin',
                        'message' => $this->messageText,
                        'is_read' => false,
                    ]);

                    if ($pelanggan->webpushr_sid) {
                        try {
                            $this->sendWebpushrNotification(
                                $pelanggan->webpushr_sid,
                                'Pesan Broadcast Admin',
                                $this->messageText,
                                url('https://layanan.jernih.net.id/dashboard/customer/chat-billing')
                            );
                        } catch (\Exception $e) {
                            Log::error('Broadcast push error', [
                                'pelanggan_id' => $pelanggan->id,
                                'error' => $e->getMessage(),
                            ]);
                        }
                    }

                    // Never stop whole broadcast because one realtime publish fails.
                    try {
                        broadcast(new MessageSent($message));
                    } catch (\Exception $e) {
                        Log::error('BroadcastAdminChat realtime publish error', [
                            'broadcast_id' => $this->broadcastId,
                            'message_id' => $message->id,
                            'pelanggan_id' => $pelanggan->id,
                            'error' => $e->getMessage(),
                        ]);
                    }

                    $done++;

                    Cache::put($cacheKey, [
                        'status' => 'running',
                        'done' => $done,
                        'total' => $this->totalPelanggan,
                        'message' => $this->messageText,
                        'updated_at' => now()->toIso8601String(),
                    ], now()->addHours(6));

                    if ($done % 100 === 0) {
                        Log::info('BroadcastAdminChat progress', [
                            'broadcast_id' => $this->broadcastId,
                            'done' => $done,
                            'total' => $this->totalPelanggan,
                        ]);
                    }
                }
            });

            Cache::put($cacheKey, [
                'status' => 'completed',
                'done' => $this->totalPelanggan,
                'total' => $this->totalPelanggan,
                'message' => $this->messageText,
                'finished_at' => now()->toIso8601String(),
            ], now()->addHours(6));

            Log::info('BroadcastAdminChat completed', [
                'broadcast_id' => $this->broadcastId,
                'done' => $this->totalPelanggan,
            ]);
        } catch (\Exception $e) {
            Cache::put($cacheKey, [
                'status' => 'failed',
                'done' => $done,
                'total' => $this->totalPelanggan,
                'message' => $this->messageText,
                'error' => $e->getMessage(),
                'failed_at' => now()->toIso8601String(),
            ], now()->addHours(6));

            Log::error('BroadcastAdminChat failed', [
                'broadcast_id' => $this->broadcastId,
                'done' => $done,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    private function sendWebpushrNotification($sid, $title, $message, $targetUrl): void
    {
        if (!$sid) return;

        $end_point = 'https://api.webpushr.com/v1/notification/send/sid';
        $http_header = [
            'Content-Type: application/json',
            'webpushrKey: 2ee12b373a17d9ba5f44683cb42d4279',
            'webpushrAuthToken: 116294',
        ];

        $req_data = [
            'title' => $title,
            'message' => substr($message, 0, 90) . (strlen($message) > 90 ? '...' : ''),
            'target_url' => $targetUrl,
            'sid' => $sid,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $http_header);
        curl_setopt($ch, CURLOPT_URL, $end_point);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($req_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 8);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        Log::info('Webpushr Broadcast', [
            'sid' => $sid,
            'http_code' => $httpCode,
            'response' => $response,
        ]);
    }
}
