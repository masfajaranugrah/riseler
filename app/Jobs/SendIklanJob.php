<?php

namespace App\Jobs;

use App\Models\Iklan;
use App\Models\Pelanggan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendIklanJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 0; // allow long running batches

    public function __construct(public string $iklanId)
    {
    }

    public function handle(): void
    {
        $iklan = Iklan::find($this->iklanId);
        if (!$iklan) {
            Log::warning('SendIklanJob: iklan not found', ['iklan_id' => $this->iklanId]);
            return;
        }

        $pelanggan = Pelanggan::whereNotNull('player_id')
            ->where('player_id', '!=', '')
            ->get();

        if ($pelanggan->isEmpty()) {
            Log::info('SendIklanJob: no pelanggan with player_id');
            $iklan->update([
                'status' => 'sent',
                'sent_at' => now(),
                'total_sent' => 0,
            ]);
            return;
        }

        $playerIds = $pelanggan->pluck('player_id')->filter()->values()->all();
        $sent = 0;

        if (!empty($playerIds)) {
            $notificationData = [
                'app_id' => env('ONESIGNAL_APP_ID'),
                'include_player_ids' => $playerIds,
                'headings' => ['en' => $iklan->title],
                'contents' => ['en' => $iklan->message],
                'data' => [
                    'type' => 'iklan',
                    'iklan_id' => $iklan->id,
                ],
            ];

            if ($iklan->image) {
                $notificationData['big_picture'] = asset('storage/' . $iklan->image);
                $notificationData['ios_attachments'] = ['id' => asset('storage/' . $iklan->image)];
            }

            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Basic ' . env('ONESIGNAL_REST_API_KEY'),
                    'Content-Type' => 'application/json',
                ])->post('https://onesignal.com/api/v1/notifications', $notificationData);

                if ($response->successful()) {
                    $sent = count($playerIds);
                } else {
                    Log::error('SendIklanJob: OneSignal response error', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);
                }
            } catch (\Throwable $e) {
                Log::error('SendIklanJob: exception sending', [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $iklan->update([
            'status' => 'sent',
            'sent_at' => now(),
            'total_sent' => $sent,
        ]);

        Log::info('SendIklanJob finished', [
            'iklan_id' => $iklan->id,
            'sent' => $sent,
            'total_targets' => count($playerIds),
        ]);
    }
}
