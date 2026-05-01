<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tagihan;
use App\Services\TelegramService;

class TestTelegramNotification extends Command
{
    protected $signature = 'telegram:test-payment {tagihan_id?}';
    protected $description = 'Test mengirim notifikasi pembayaran Telegram';

    public function handle()
    {
        // MOCK DATA for testing if database is not accessible or empty
        $tagihan = new Tagihan();
        $tagihan->id = 99999;
        $tagihan->tanggal_mulai = now();
        
        $pelanggan = new \App\Models\Pelanggan();
        $pelanggan->nama_lengkap = 'Test User (Mock)';
        $pelanggan->nomer_id = 'TEST.123';
        $tagihan->setRelation('pelanggan', $pelanggan);
        
        $paket = new \App\Models\Paket();
        $paket->harga = 150000;
        $tagihan->setRelation('paket', $paket);

        // if ($id) {
        //     $tagihan = Tagihan::with(['pelanggan', 'paket'])->find($id);
        // } else {
        //     $tagihan = Tagihan::with(['pelanggan', 'paket'])->latest()->first();
        // }

        // if (!$tagihan) {
        //     $this->error('Tidak ada data tagihan ditemukan.');
        //     return;
        // }

        $this->info("Mengirim notifikasi untuk Tagihan ID: {$tagihan->id} - Pelanggan: {$tagihan->pelanggan->nama_lengkap}");

        try {
            $service = new TelegramService();
            $service->sendPaymentNotification($tagihan);
            $this->info('Notifikasi berhasil dikirim!');
        } catch (\Exception $e) {
            $this->error('Gagal mengirim notifikasi: ' . $e->getMessage());
        }
    }
}
