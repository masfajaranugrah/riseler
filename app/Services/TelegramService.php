<?php

namespace App\Services;

use Telegram\Bot\Api;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TelegramService
{
    protected $telegram;
    protected $chatId;

    public function __construct()
    {
        $token = env('TELEGRAM_BOT_TOKEN_TAGIHAN');
        $this->chatId = env('TELEGRAM_ADMIN_ID'); // Target admin ID

        if ($token) {
            $this->telegram = new Api($token);
        }
    }

    public function sendPaymentNotification($tagihan)
    {
        if (!$this->telegram || !$this->chatId) {
            Log::warning('Telegram bot token or admin ID not configured.');
            return;
        }

        try {
            $pelanggan = $tagihan->pelanggan;
            $paket = $tagihan->paket;
            
            // Format bulan yang dibayar dari tanggal_mulai
            $bulan = Carbon::parse($tagihan->tanggal_mulai)->locale('id')->translatedFormat('F Y');
            
            $message = "🔔 *PEMBAYARAN MASUK MENUNGGU VERIFIKASI*\n\n" .
                       "Nomer ID: `{$pelanggan->nomer_id}`\n" .
                       "Nama: *{$pelanggan->nama_lengkap}*\n" .
                       "Sudah membayar bulan: *{$bulan}*\n\n" .
                       "Mohon segera diverifikasi.";

            $this->telegram->sendMessage([
                'chat_id' => $this->chatId,
                'text' => $message,
                'parse_mode' => 'Markdown'
            ]);

            Log::info("Telegram notification sent for tagihan ID: {$tagihan->id}");

        } catch (\Exception $e) {
            Log::error("Failed to send Telegram notification: " . $e->getMessage());
        }
    }
}
