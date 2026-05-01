<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Telegram\Bot\Api;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Pelanggan;
use Carbon\Carbon;

class TelegramRunCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run Telegram Bot Polling (Long Polling) for Customer Stats';

    protected $telegram;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Starting Telegram Bot Polling...");
        
        $token = env('TELEGRAM_BOT_TOKEN_TECHNICAL');
        if (!$token) {
            $this->error("TELEGRAM_BOT_TOKEN_TECHNICAL not found in .env");
            return;
        }

        $this->telegram = new Api($token);
        
        $offset = 0;

        while(true) {
            try {
                // Long Polling with 30s timeout
                $updates = $this->telegram->getUpdates(['offset' => $offset, 'timeout' => 30]);

                foreach($updates as $update) {
                    $offset = $update->getUpdateId() + 1;
                    
                    $message = $update->getMessage();
                    if (!$message) continue;
                    
                    $chatId = $message->getChat()->getId();
                    $text = $message->getText();

                    if (!$text) continue;

                    // Support /start, /stats, or just "stats"
                    if (in_array(strtolower($text), ['/start', '/stats', 'stats', 'cek', 'info'])) {
                         $this->sendStats($chatId);
                    }

                    if (in_array(strtolower($text), [
                        '/progress-pelanggan',
                        '/pelanggan-progress',
                        '/progres',
                        'progress pelanggan',
                        'progres pelanggan',
                        'cek progres pelanggan',
                        'cek progress pelanggan'
                    ])) {
                        $this->sendCustomerProgressStats($chatId);
                    }

                    // Cek detail "Lainnya" (Belum Pernah Login & Non-JMK)
                    if (in_array(strtolower($text), ['cek lainnya', '/lainnya', 'info lainnya'])) {
                        $this->sendLainnyaDetails($chatId);
                    }

                    // Cek duplikat nomer_id
                    if (in_array(strtolower($text), ['cek duplikat', '/duplikat', 'info duplikat'])) {
                        $this->sendDuplicateDetails($chatId);
                    }

                    // Cek chat belum dibalas
                    if (in_array(strtolower($text), ['cek chat', '/chat', 'info chat', 'pesan'])) {
                        $this->sendChatStats($chatId);
                    }

                    // Cek status sistem
                    if (in_array(strtolower($text), ['cek status', '/status', 'info status', 'status sistem'])) {
                        $this->sendSystemStatus($chatId);
                    }

                    // Cek health server
                    if (in_array(strtolower($text), ['cek health', '/health', 'info health', 'health server', 'kesehatan server'])) {
                        $this->sendServerHealth($chatId);
                    }

                    // Cek kecepatan internet
                    if (in_array(strtolower($text), ['cek speed', '/speed', 'info speed', 'kecepatan internet', 'internet speed'])) {
                        $this->sendInternetSpeed($chatId);
                    }

                    // Cek status aktif pelanggan (Berdasarkan Tanggal Berakhir)
                    if (in_array(strtolower($text), ['cek status aktif', '/active', 'info aktif', 'aktif', 'status aktif', 'cek aktif'])) {
                        $this->sendActiveStatus($chatId);
                    }

                    // 🚀 ADVANCED FEATURES
                    
                    // Dashboard
                    if (in_array(strtolower($text), ['/dashboard', 'dashboard', 'ringkasan'])) {
                        $this->sendDashboard($chatId);
                    }

                    // Revenue Analytics
                    if (in_array(strtolower($text), ['/revenue', 'revenue', 'pendapatan'])) {
                        $this->sendRevenueAnalytics($chatId);
                    }

                    // Tagihan Monitoring
                    if (in_array(strtolower($text), ['/tagihan-today', 'tagihan hari ini', '/tagihan'])) {
                        $this->sendTagihanToday($chatId);
                    }

                    // Cek pelanggan yang belum punya tagihan bulan ini
                    if (in_array(strtolower($text), [
                        '/belum-tagihan',
                        'belum tagihan',
                        'cek belum tagihan',
                        'tagihan bulan ini',
                        '/tagihan-bulan-ini'
                    ])) {
                        $this->sendCustomersWithoutCurrentMonthBill($chatId);
                    }

                    // Predictive Analytics
                    if (in_array(strtolower($text), ['/predict', 'prediksi', 'forecasting'])) {
                        $this->sendPredictiveAnalytics($chatId);
                    }

                    // Database Performance
                    if (in_array(strtolower($text), ['/db-perf', 'database performance', '/dbperf'])) {
                        $this->sendDatabasePerformance($chatId);
                    }

                    // Error Analytics
                    if (in_array(strtolower($text), ['/errors', 'error log', '/error'])) {
                        $this->sendErrorAnalytics($chatId);
                    }

                    // Customer Insights
                    if (in_array(strtolower($text), ['/insights', 'customer insights', 'analisis pelanggan'])) {
                        $this->sendCustomerInsights($chatId);
                    }

                    // Smart Alerts
                    if (in_array(strtolower($text), ['/alerts', 'peringatan', 'warning'])) {
                        $this->sendSmartAlerts($chatId);
                    }

                    // Cek ID Saya (Untuk konfigurasi .env)
                    if (in_array(strtolower($text), ['/myid', 'myid', 'id saya', 'cek id'])) {
                        $this->telegram->sendMessage([
                            'chat_id' => $chatId,
                            'text' => "🆔 ID Telegram Anda: `{$chatId}`\n\nSilakan salin ID ini dan masukkan ke file .env:\n`TELEGRAM_ADMIN_ID={$chatId}`",
                            'parse_mode' => 'Markdown'
                        ]);
                    }

                    // Help menu
                    if (in_array(strtolower($text), ['/help', 'help', 'bantuan', '?'])) {
                        $this->sendHelpMenu($chatId);
                    }
                }

            } catch (\Exception $e) {
                $this->error("Error: " . $e->getMessage());
                sleep(5);
            }
        }
    }

    private function sendLainnyaDetails($chatId)
    {
        try {
            $lainnyaIds = \App\Models\Pelanggan::doesntHave('loginStatus')
                ->where('nomer_id', 'not like', '%JMK%')
                ->pluck('nomer_id')
                ->toArray();

            if (empty($lainnyaIds)) {
                $message = "✅ Tidak ada pelanggan kategori 'Lainnya' (Non-JMK & Belum Login).";
            } else {
                $list = implode("\n- ", $lainnyaIds);
                $count = count($lainnyaIds);
                $message = "📋 *Daftar Pelanggan 'Lainnya' (Belum Login)*\n" .
                           "Total: {$count}\n\n" .
                           "- " . $list;
            }

            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown'
            ]);

            $this->info("Sent details 'Lainnya' to $chatId");

        } catch (\Exception $e) {
            $this->error("Failed to get lainnya details: " . $e->getMessage());
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "❌ Error: " . $e->getMessage()
            ]);
        }
    }

    private function sendDuplicateDetails($chatId)
    {
        try {
            // Kita trim nomer_id karena ada masalah spasi di database
            $duplicates = \App\Models\Pelanggan::select(DB::raw('trim(nomer_id) as trimmed_id'), DB::raw('count(*) as total'))
                ->groupBy('trimmed_id')
                ->having('total', '>', 1)
                ->get();

            if ($duplicates->isEmpty()) {
                $message = "✅ Tidak ditemukan Nomer ID yang duplikat.";
            } else {
                $message = "⚠️ *Daftar Nomer ID Duplikat*\n\n";
                foreach ($duplicates as $dup) {
                    $message .= "- `{$dup->trimmed_id}`: *{$dup->total}* kali muncul\n";
                }
            }

            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown'
            ]);

            $this->info("Sent duplicate details to $chatId");

        } catch (\Exception $e) {
            $this->error("Failed to get duplicate details: " . $e->getMessage());
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "❌ Error: " . $e->getMessage()
            ]);
        }
    }

    private function sendChatStats($chatId)
    {
        try {
            // Hitung pesan belum dibaca (unread) yang ditujukan ke Admin/CS
            // Kita join dengan tabel users untuk memfilter berdasarkan role
            $unreadStats = \App\Models\Message::where('is_read', false)
                ->join('users', 'messages.receiver_id', '=', 'users.id')
                ->whereIn('users.role', ['admin', 'administrator', 'customer_service'])
                ->select('users.role', DB::raw('count(*) as total'))
                ->groupBy('users.role')
                ->get();

            $totalUnread = $unreadStats->sum('total');

            if ($totalUnread == 0) {
                $message = "✅ Semua pesan sudah dibaca/dibalas.";
            } else {
                $message = "💬 *Statistik Chat Belum Dibalas*\n\n";
                foreach ($unreadStats as $stat) {
                    $roleName = ($stat->role == 'customer_service') ? 'CS' : 'Admin';
                    $message .= "• {$roleName}: *{$stat->total}* pesan\n";
                }
                $message .= "\nTotal: *{$totalUnread}* pesan belum dibalas.";
            }

            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown'
            ]);

            $this->info("Sent chat stats to $chatId");

        } catch (\Exception $e) {
            $this->error("Failed to get chat stats: " . $e->getMessage());
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "❌ Error: " . $e->getMessage()
            ]);
        }
    }

    private function sendStats($chatId)
    {
        try {
            // 1. Total Customers
            $totalCustomers = \App\Models\Pelanggan::count();

            // 2. Active Customers (Online)
            $activeCount = \App\Models\Pelanggan::whereHas('loginStatus', function($q) {
                $q->where('is_active', true);
            })->count();

            // 3. Never Logged In (Belum Pernah Login) - Global
            // $neverLoginCount = \App\Models\Pelanggan::doesntHave('loginStatus')->count();

            // 3a. Belum Pernah Login (JMK-GK)
            $neverLoginJmkGk = \App\Models\Pelanggan::doesntHave('loginStatus')
                ->where('nomer_id', 'like', '%JMK-GK%')
                ->count();

            // 3b. Belum Pernah Login (JMK biasa, exclude JMK-GK)
            $neverLoginJmk = \App\Models\Pelanggan::doesntHave('loginStatus')
                ->where('nomer_id', 'like', '%JMK%')
                ->where('nomer_id', 'not like', '%JMK-GK%')
                ->count();
            
           $lainnyaCount = \App\Models\Pelanggan::doesntHave('loginStatus')
                ->where('nomer_id', 'not like', '%JMK%')
                ->count();


            $totalNeverLogin = $neverLoginJmkGk + $neverLoginJmk + $lainnyaCount;

            // 4. Inactive (Pernah Login tapi sekarang offline)
            $inactivePernahLogin = \App\Models\Pelanggan::whereHas('loginStatus', function($q) {
                $q->where('is_active', false);
            })->count();

            // 5. Status Registrasi - Approve & Belum Approve
            $totalApprove = \App\Models\Pelanggan::where('status', 'approve')->count();
            $totalBelumApprove = \App\Models\Pelanggan::where('status', '!=', 'approve')
                ->orWhereNull('status')
                ->count();

            // 5a. Breakdown Belum Approve berdasarkan status
            $totalPending = \App\Models\Pelanggan::where('status', 'pending')->count();
            $totalProses = \App\Models\Pelanggan::where('status', 'proses')->count();
            $totalReject = \App\Models\Pelanggan::where('status', 'reject')->count();

            $message = "📊 *Statistik Pelanggan (Real-time)*\n\n" .
                       "🟢 *Online (Active):* {$activeCount}\n" .
                       "🔴 *Offline (Pernah Login):* {$inactivePernahLogin}\n" .
                       "⚪ *Belum Pernah Login Total:* {$totalNeverLogin}\n" .
                       "    ├ 🔹 JMK: {$neverLoginJmk}\n" .
                       "    ├ 🔸 JMK-GK: {$neverLoginJmkGk}\n" .
                       "👥 *Total Semua Pelanggan:* {$totalCustomers}\n\n" .
                       "━━━━━━━━━━━━━━━━━━━━━━\n" .
                       "📋 *Status Registrasi*\n\n" .
                       "✅ *Total Pelanggan Approve:* {$totalApprove}\n" .
                       "⏳ *Total Belum Di-Approve:* {$totalBelumApprove}\n" .
                       "    ├ 🟡 Pending: {$totalPending}\n" .
                       "    ├ 🔄 Proses: {$totalProses}\n" .
                       "    └ ❌ Reject: {$totalReject}";

            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown'
            ]);

            $this->info("Sent stats to $chatId");

        } catch (\Exception $e) {
            $this->error("Failed to calc stats: " . $e->getMessage());
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "❌ Terjadi kesalahan saat mengambil data: " . $e->getMessage()
            ]);
        }
    }

    private function sendCustomerProgressStats($chatId)
    {
        try {
            // Fungsi filter wilayah
            $queryRegion = function($region) {
                if ($region === 'Klaten') {
                    return Pelanggan::where(function($q) {
                        $q->where('nomer_id', 'like', 'PB-%')
                          ->orWhere('nomer_id', 'like', 'JMK%');
                    })
                    ->where('nomer_id', 'not like', '%-GK%')
                    ->where('nomer_id', 'not like', '%-BY%');
                }
                if ($region === 'Gunung Kidul') {
                    return Pelanggan::where(function($q) {
                        $q->where('nomer_id', 'like', 'PB-GK-%')
                          ->orWhere('nomer_id', 'like', '%-GK%');
                    });
                }
                if ($region === 'Boyolali') {
                    return Pelanggan::where(function($q) {
                        $q->where('nomer_id', 'like', 'PB-BY-%')
                          ->orWhere('nomer_id', 'like', '%-BY%');
                    });
                }
            };

            // Klaten
            $klatenBelum = $queryRegion('Klaten')->where(function ($q) {
                $q->whereNull('progres')->orWhere('progres', '')->orWhere('progres', Pelanggan::PROGRES_BELUM_DIPROSES);
            })->whereIn('status', ['proses', 'pending'])->count();
            
            $klatenTarik = $queryRegion('Klaten')->where('progres', 'Tarik Kabel')->whereIn('status', ['proses', 'pending'])->count();
            $klatenAktivasi = $queryRegion('Klaten')->where('progres', 'Aktivasi')->whereIn('status', ['proses', 'pending'])->count();
            $klatenRegistrasi = $queryRegion('Klaten')->where('progres', 'Registrasi')->whereIn('status', ['proses', 'pending', 'approve'])->count();

            // Gunung Kidul
            $gkBelum = $queryRegion('Gunung Kidul')->where(function ($q) {
                $q->whereNull('progres')->orWhere('progres', '')->orWhere('progres', Pelanggan::PROGRES_BELUM_DIPROSES);
            })->whereIn('status', ['proses', 'pending'])->count();
            
            $gkTarik = $queryRegion('Gunung Kidul')->where('progres', 'Tarik Kabel')->whereIn('status', ['proses', 'pending'])->count();
            $gkAktivasi = $queryRegion('Gunung Kidul')->where('progres', 'Aktivasi')->whereIn('status', ['proses', 'pending'])->count();
            $gkRegistrasi = $queryRegion('Gunung Kidul')->where('progres', 'Registrasi')->whereIn('status', ['proses', 'pending', 'approve'])->count();

            // Boyolali
            $byBelum = $queryRegion('Boyolali')->where(function ($q) {
                $q->whereNull('progres')->orWhere('progres', '')->orWhere('progres', Pelanggan::PROGRES_BELUM_DIPROSES);
            })->whereIn('status', ['proses', 'pending'])->count();
            
            $byTarik = $queryRegion('Boyolali')->where('progres', 'Tarik Kabel')->whereIn('status', ['proses', 'pending'])->count();
            $byAktivasi = $queryRegion('Boyolali')->where('progres', 'Aktivasi')->whereIn('status', ['proses', 'pending'])->count();
            $byRegistrasi = $queryRegion('Boyolali')->where('progres', 'Registrasi')->whereIn('status', ['proses', 'pending', 'approve'])->count();

            // Total
            $totalBelumDiproses = $klatenBelum + $gkBelum + $byBelum;
            $totalTarikKabel = $klatenTarik + $gkTarik + $byTarik;
            $totalAktivasi = $klatenAktivasi + $gkAktivasi + $byAktivasi;
            $totalRegistrasi = $klatenRegistrasi + $gkRegistrasi + $byRegistrasi;

            $message = "📶 *Total Pelanggan Status Progress*\n\n" .
                "⏳ *Belum Diproses:* {$totalBelumDiproses}\n" .
                "   ├ 📍 Klaten: {$klatenBelum}\n" .
                "   ├ 📍 Gunung Kidul: {$gkBelum}\n" .
                "   └ 📍 Boyolali: {$byBelum}\n\n" .
                "🔌 *Total Ditarik:* {$totalTarikKabel}\n" .
                "   ├ 📍 Klaten: {$klatenTarik}\n" .
                "   ├ 📍 Gunung Kidul: {$gkTarik}\n" .
                "   └ 📍 Boyolali: {$byTarik}\n\n" .
                "⚡ *Total Aktivasi:* {$totalAktivasi}\n" .
                "   ├ 📍 Klaten: {$klatenAktivasi}\n" .
                "   ├ 📍 Gunung Kidul: {$gkAktivasi}\n" .
                "   └ 📍 Boyolali: {$byAktivasi}\n\n" .
                "📝 *Total Register:* {$totalRegistrasi}\n" .
                "   ├ 📍 Klaten: {$klatenRegistrasi}\n" .
                "   ├ 📍 Gunung Kidul: {$gkRegistrasi}\n" .
                "   └ 📍 Boyolali: {$byRegistrasi}";

            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown'
            ]);

            $this->info("Sent customer progress stats to $chatId");
        } catch (\Exception $e) {
            $this->error("Failed to get customer progress stats: " . $e->getMessage());
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "❌ Terjadi kesalahan saat mengambil statistik progres pelanggan: " . $e->getMessage()
            ]);
        }
    }

    private function sendSystemStatus($chatId)
    {
        try {
            $message = "🔍 *Status Sistem*\n\n";
            
            // Cek koneksi database
            try {
                DB::connection()->getPdo();
                $dbStatus = "✅ Database: *Online*";
            } catch (\Exception $e) {
                $dbStatus = "❌ Database: *Down*\n⚠️ *WARNING: Sistem tidak dapat terhubung ke database!*";
            }
            
            // Cek apakah aplikasi berjalan
            $appStatus = "✅ Aplikasi: *Running*";
            
            // Cek waktu uptime server
            $uptime = $this->getServerUptime();
            
            // Cek beban server
            $load = sys_getloadavg();
            $cpuCores = $this->getCpuCores();
            $loadAvg = round($load[0], 2);
            $loadPercentage = ($cpuCores > 0) ? round(($loadAvg / $cpuCores) * 100, 2) : 0;
            
            $loadStatus = "";
            if ($loadPercentage > 80) {
                $loadStatus = "\n⚠️ *WARNING: Server sedang kecapean! CPU Load tinggi: {$loadPercentage}%*";
            } elseif ($loadPercentage > 60) {
                $loadStatus = "\n⚡ *INFO: Load cukup tinggi ({$loadPercentage}%), monitor terus!*";
            }
            
            $message .= $dbStatus . "\n";
            $message .= $appStatus . "\n";
            $message .= "⏱ Uptime: {$uptime}\n";
            $message .= "📊 CPU Load: {$loadAvg} ({$loadPercentage}%)";
            $message .= $loadStatus;
            
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown'
            ]);
            
            $this->info("Sent system status to $chatId");
            
        } catch (\Exception $e) {
            $this->error("Failed to get system status: " . $e->getMessage());
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "❌ Error: " . $e->getMessage()
            ]);
        }
    }

    private function sendServerHealth($chatId)
    {
        try {
            $message = "🏥 *Kesehatan Server*\n\n";
            $warnings = [];
            
            // CPU Load
            $load = sys_getloadavg();
            $cpuCores = $this->getCpuCores();
            $loadAvg = round($load[0], 2);
            $loadPercentage = ($cpuCores > 0) ? round(($loadAvg / $cpuCores) * 100, 2) : 0;
            
            $cpuIcon = "🟢";
            if ($loadPercentage > 80) {
                $cpuIcon = "🔴";
                $warnings[] = "CPU Load sangat tinggi ({$loadPercentage}%)!";
            } elseif ($loadPercentage > 60) {
                $cpuIcon = "🟡";
                $warnings[] = "CPU Load tinggi ({$loadPercentage}%)";
            }
            
            $message .= "{$cpuIcon} *CPU Load:* {$loadAvg} / {$cpuCores} cores ({$loadPercentage}%)\n";
            
            // Memory Usage
            $memoryData = $this->getMemoryUsage();
            $memoryPercentage = $memoryData['percentage'];
            
            $memIcon = "🟢";
            if ($memoryPercentage > 85) {
                $memIcon = "🔴";
                $warnings[] = "Memory usage sangat tinggi ({$memoryPercentage}%)!";
            } elseif ($memoryPercentage > 70) {
                $memIcon = "🟡";
                $warnings[] = "Memory usage tinggi ({$memoryPercentage}%)";
            }
            
            $message .= "{$memIcon} *Memory:* {$memoryData['used']} / {$memoryData['total']} ({$memoryPercentage}%)\n";
            
            // Disk Usage
            $diskData = $this->getDiskUsage();
            $diskPercentage = $diskData['percentage'];
            
            $diskIcon = "🟢";
            if ($diskPercentage > 90) {
                $diskIcon = "🔴";
                $warnings[] = "Disk space hampir penuh ({$diskPercentage}%)!";
            } elseif ($diskPercentage > 80) {
                $diskIcon = "🟡";
                $warnings[] = "Disk space tinggi ({$diskPercentage}%)";
            }
            
            $message .= "{$diskIcon} *Disk:* {$diskData['used']} / {$diskData['total']} ({$diskPercentage}%)\n";
            
            // Overall Status
            if (!empty($warnings)) {
                $message .= "\n⚠️ *WARNINGS:*\n";
                foreach ($warnings as $warning) {
                    $message .= "• {$warning}\n";
                }
            } else {
                $message .= "\n✅ *Status: Semua normal*";
            }
            
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown'
            ]);
            
            $this->info("Sent server health to $chatId");
            
        } catch (\Exception $e) {
            $this->error("Failed to get server health: " . $e->getMessage());
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "❌ Error: " . $e->getMessage()
            ]);
        }
    }

    private function sendInternetSpeed($chatId)
    {
        try {
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "⏳ Mengecek kecepatan internet... Mohon tunggu..."
            ]);
            
            $speedData = $this->checkInternetSpeed();
            
            $message = "🌐 *Kecepatan Internet Server*\n\n";
            $message .= "⬇️ *Download:* {$speedData['download_mbps']} Mbps ({$speedData['download']} MB/s)\n";
            $message .= "⬆️ *Upload:* {$speedData['upload_mbps']} Mbps ({$speedData['upload']} MB/s)\n";
            $message .= "🏓 *Ping:* {$speedData['ping']} ms\n";
            
            // Warning jika koneksi lambat
            $warnings = [];
            // Preserve previous threshold intent (5 MB/s ~= 40 Mbps, 2 MB/s ~= 16 Mbps)
            if ($speedData['download_mbps'] < 40) {
                $warnings[] = "Download speed sangat lambat!";
            }
            if ($speedData['upload_mbps'] < 16) {
                $warnings[] = "Upload speed sangat lambat!";
            }
            if ($speedData['ping'] > 100) {
                $warnings[] = "Ping tinggi, koneksi mungkin tidak stabil!";
            }
            
            if (!empty($warnings)) {
                $message .= "\n⚠️ *WARNINGS:*\n";
                foreach ($warnings as $warning) {
                    $message .= "• {$warning}\n";
                }
            } else {
                $message .= "\n✅ *Status: Koneksi normal*";
            }
            
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown'
            ]);
            
            $this->info("Sent internet speed to $chatId");
            
        } catch (\Exception $e) {
            $this->error("Failed to check internet speed: " . $e->getMessage());
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "❌ Error saat mengecek kecepatan internet: " . $e->getMessage()
            ]);
        }
    }

    private function sendActiveStatus($chatId)
    {
        try {
            $today = Carbon::today();
            
            // 1. Active (Expired Date >= Today)
            $activeCount = \App\Models\Pelanggan::whereDate('tanggal_berakhir', '>=', $today)->count();
            
            // 2. Expired (Expired Date < Today)
            $expiredCount = \App\Models\Pelanggan::whereDate('tanggal_berakhir', '<', $today)->count();
            
            // 3. No Date (NULL) - Belum pernah berlangganan atau data kosong
            $noDateCount = \App\Models\Pelanggan::whereNull('tanggal_berakhir')->count();
            
            // Total
            $total = $activeCount + $expiredCount + $noDateCount;
            
            $message = "📅 *Status Aktif Pelanggan (Per {$today->format('d M Y')})*\n\n" .
                       "✅ *Total:* {$activeCount} Pelanggan Aktif\n\n" .
                       "ℹ️ _Dihitung berdasarkan tanggal berakhir paket >= hari ini._";

            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown'
            ]);

            $this->info("Sent active status stats to $chatId");

        } catch (\Exception $e) {
            $this->error("Failed to check active status: " . $e->getMessage());
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "❌ Error saat mengecek status aktif: " . $e->getMessage()
            ]);
        }
    }

    private function getServerUptime()
    {
        try {
            if (PHP_OS_FAMILY === 'Darwin' || PHP_OS_FAMILY === 'Linux') {
                $uptime = shell_exec('uptime');
                if (preg_match('/up\s+(.+?),\s+\d+\s+user/', $uptime, $matches)) {
                    return trim($matches[1]);
                }
            }
            return 'N/A';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    private function getCpuCores()
    {
        try {
            if (PHP_OS_FAMILY === 'Darwin') {
                return (int) shell_exec('sysctl -n hw.ncpu');
            } elseif (PHP_OS_FAMILY === 'Linux') {
                return (int) shell_exec('nproc');
            }
            return 1;
        } catch (\Exception $e) {
            return 1;
        }
    }

    private function getMemoryUsage()
    {
        try {
            if (PHP_OS_FAMILY === 'Darwin') {
                $memInfo = shell_exec('vm_stat | grep "Pages free\|Pages active\|Pages inactive\|Pages speculative\|Pages wired"');
                // Simplified - actual calculation would be more complex
                return [
                    'used' => 'N/A',
                    'total' => 'N/A',
                    'percentage' => 0
                ];
            } elseif (PHP_OS_FAMILY === 'Linux') {
                $free = shell_exec('free -m');
                if (preg_match('/Mem:\s+(\d+)\s+(\d+)/', $free, $matches)) {
                    $total = (int) $matches[1];
                    $used = (int) $matches[2];
                    $percentage = round(($used / $total) * 100, 2);
                    return [
                        'used' => $used . ' MB',
                        'total' => $total . ' MB',
                        'percentage' => $percentage
                    ];
                }
            }
            
            // Fallback untuk sistem yang tidak support
            $memUsed = round(memory_get_usage(true) / 1024 / 1024, 2);
            $memLimit = ini_get('memory_limit');
            return [
                'used' => $memUsed . ' MB',
                'total' => $memLimit,
                'percentage' => 0
            ];
        } catch (\Exception $e) {
            return [
                'used' => 'N/A',
                'total' => 'N/A',
                'percentage' => 0
            ];
        }
    }

    private function getDiskUsage()
    {
        try {
            $path = base_path();
            $total = disk_total_space($path);
            $free = disk_free_space($path);
            $used = $total - $free;
            $percentage = round(($used / $total) * 100, 2);
            
            return [
                'used' => $this->formatBytes($used),
                'total' => $this->formatBytes($total),
                'percentage' => $percentage
            ];
        } catch (\Exception $e) {
            return [
                'used' => 'N/A',
                'total' => 'N/A',
                'percentage' => 0
            ];
        }
    }

    private function checkInternetSpeed()
    {
        try {
            if (!function_exists('curl_init')) {
                throw new \RuntimeException('Ekstensi cURL tidak tersedia.');
            }

            $downloadSamples = [];
            for ($i = 0; $i < 2; $i++) {
                $downloadSamples[] = $this->runDownloadSpeedTest(
                    'https://speed.cloudflare.com/__down?bytes=25000000&r=' . rawurlencode((string) microtime(true) . $i)
                );
            }

            $uploadSamples = [];
            for ($i = 0; $i < 2; $i++) {
                $uploadSamples[] = $this->runUploadSpeedTest('https://speed.cloudflare.com/__up', 5 * 1024 * 1024);
            }

            $pingMs = $this->runHttpPing('https://www.google.com/generate_204', 4);
            $downloadMbps = $this->medianFloat($downloadSamples);
            $uploadMbps = $this->medianFloat($uploadSamples);

            return [
                'download' => round($downloadMbps / 8, 2), // MB/s
                'upload' => round($uploadMbps / 8, 2), // MB/s
                'ping' => round($pingMs, 2),
                'download_mbps' => round($downloadMbps, 2),
                'upload_mbps' => round($uploadMbps, 2)
            ];
        } catch (\Throwable $e) {
            return [
                'download' => '0',
                'upload' => '0',
                'ping' => '999',
                'download_mbps' => 0,
                'upload_mbps' => 0
            ];
        }
    }

    private function runHttpPing(string $url, int $attempts = 4): float
    {
        $latencies = [];

        for ($i = 0; $i < $attempts; $i++) {
            $ch = curl_init();
            $start = microtime(true);

            curl_setopt_array($ch, [
                CURLOPT_URL => $url . '?r=' . rawurlencode((string) microtime(true) . $i),
                CURLOPT_NOBODY => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_HTTPHEADER => [
                    'Cache-Control: no-cache',
                    'Pragma: no-cache',
                ],
            ]);

            curl_exec($ch);
            if (curl_errno($ch)) {
                $error = curl_error($ch);
                curl_close($ch);
                throw new \RuntimeException('Ping test gagal: ' . $error);
            }

            $statusCode = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
            curl_close($ch);

            if ($statusCode >= 400) {
                throw new \RuntimeException('Ping endpoint merespons status ' . $statusCode);
            }

            $latencies[] = (microtime(true) - $start) * 1000;
            usleep(100000);
        }

        return $this->medianFloat($latencies);
    }

    private function runDownloadSpeedTest(string $url): float
    {
        $bytesReceived = 0;
        $ch = curl_init();
        $start = microtime(true);

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_ENCODING => 'identity',
            CURLOPT_HTTPHEADER => [
                'Cache-Control: no-cache',
                'Pragma: no-cache',
            ],
            CURLOPT_WRITEFUNCTION => function ($curl, $data) use (&$bytesReceived) {
                $length = strlen($data);
                $bytesReceived += $length;
                return $length;
            },
        ]);

        curl_exec($ch);
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new \RuntimeException('Download test gagal: ' . $error);
        }

        $statusCode = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);

        if ($statusCode >= 400) {
            throw new \RuntimeException('Download endpoint merespons status ' . $statusCode);
        }

        $elapsed = max(microtime(true) - $start, 0.001);
        if ($bytesReceived <= 0) {
            throw new \RuntimeException('Tidak ada data download yang diterima.');
        }

        return ($bytesReceived * 8) / $elapsed / 1000000;
    }

    private function runUploadSpeedTest(string $url, int $payloadBytes = 5242880): float
    {
        $payload = random_bytes($payloadBytes);
        $ch = curl_init();
        $start = microtime(true);

        curl_setopt_array($ch, [
            CURLOPT_URL => $url . '?r=' . rawurlencode((string) microtime(true)),
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_RETURNTRANSFER => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_ENCODING => 'identity',
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/octet-stream',
                'Cache-Control: no-cache',
                'Pragma: no-cache',
                'Expect:',
            ],
            CURLOPT_WRITEFUNCTION => function ($curl, $data) {
                return strlen($data);
            },
        ]);

        curl_exec($ch);
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new \RuntimeException('Upload test gagal: ' . $error);
        }

        $statusCode = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);

        if ($statusCode >= 400) {
            throw new \RuntimeException('Upload endpoint merespons status ' . $statusCode);
        }

        $elapsed = max(microtime(true) - $start, 0.001);
        return ($payloadBytes * 8) / $elapsed / 1000000;
    }

    private function medianFloat(array $values): float
    {
        if (empty($values)) {
            return 0;
        }

        sort($values, SORT_NUMERIC);
        $count = count($values);
        $middle = intdiv($count, 2);

        if ($count % 2 === 0) {
            return ($values[$middle - 1] + $values[$middle]) / 2;
        }

        return $values[$middle];
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    // ================== 🚀 ADVANCED FEATURES ==================

    private function sendDashboard($chatId)
    {
        try {
            $message = "📊 *BUSINESS DASHBOARD*\n";
            $message .= "━━━━━━━━━━━━━━━━━━━━\n\n";
            
            // Revenue Today
            $revenueToday = \App\Models\Tagihan::whereDate('tagihans.updated_at', Carbon::today())
                ->where('tagihans.status_pembayaran', 'lunas')
                ->join('pakets', 'tagihans.paket_id', '=', 'pakets.id')
                ->sum('pakets.harga');
            
            // Revenue This Month
            $revenueMonth = \App\Models\Tagihan::whereMonth('tagihans.updated_at', Carbon::now()->month)
                ->whereYear('tagihans.updated_at', Carbon::now()->year)
                ->where('tagihans.status_pembayaran', 'lunas')
                ->join('pakets', 'tagihans.paket_id', '=', 'pakets.id')
                ->sum('pakets.harga');
            
            // Outstanding Bills
            $outstanding = \App\Models\Tagihan::where('tagihans.status_pembayaran', 'belum bayar')
                ->join('pakets', 'tagihans.paket_id', '=', 'pakets.id')
                ->sum('pakets.harga');
            
            // Active Customers
            $activeCustomers = \App\Models\Pelanggan::whereHas('loginStatus', function($q) {
                $q->where('is_active', true);
            })->count();
            
            // Total Customers
            $totalCustomers = \App\Models\Pelanggan::count();
            
            // Bills Due Today
            $billsDueToday = \App\Models\Tagihan::whereDate('tanggal_berakhir', Carbon::today())
                ->where('status_pembayaran', 'belum bayar')
                ->count();
            
            // System Health Score
            $healthScore = $this->calculateHealthScore();
            $healthIcon = $healthScore >= 80 ? '🟢' : ($healthScore >= 60 ? '🟡' : '🔴');
            
            $message .= "💰 *REVENUE*\n";
            $message .= "  • Hari ini: Rp " . number_format($revenueToday, 0, ',', '.') . "\n";
            $message .= "  • Bulan ini: Rp " . number_format($revenueMonth, 0, ',', '.') . "\n";
            $message .= "  • Tunggakan: Rp " . number_format($outstanding, 0, ',', '.') . "\n\n";
            
            $message .= "👥 *CUSTOMERS*\n";
            $message .= "  • Online: {$activeCustomers}\n";
            $message .= "  • Total: {$totalCustomers}\n";
            $message .= "  • Engagement: " . round(($activeCustomers / max($totalCustomers, 1)) * 100, 1) . "%\n\n";
            
            $message .= "📋 *TAGIHAN*\n";
            $message .= "  • Jatuh tempo hari ini: {$billsDueToday}\n\n";
            
            $message .= "{$healthIcon} *SYSTEM HEALTH*\n";
            $message .= "  • Score: {$healthScore}/100\n";
            
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown'
            ]);
            
            $this->info("Sent dashboard to $chatId");
            
        } catch (\Exception $e) {
            $this->error("Dashboard error: " . $e->getMessage());
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "❌ Error: " . $e->getMessage()
            ]);
        }
    }

    private function sendRevenueAnalytics($chatId)
    {
        try {
            $message = "💰 *REVENUE ANALYTICS*\n";
            $message .= "━━━━━━━━━━━━━━━━━━━━\n\n";
            
            // Last 7 days revenue
            $last7Days = [];
            $totalLast7 = 0;
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $revenue = \App\Models\Tagihan::whereDate('tagihans.updated_at', $date)
                    ->where('tagihans.status_pembayaran', 'lunas')
                    ->join('pakets', 'tagihans.paket_id', '=', 'pakets.id')
                    ->sum('pakets.harga');
                $last7Days[] = $revenue;
                $totalLast7 += $revenue;
            }
            
            $avgDaily = $totalLast7 / 7;
            
            // This month vs last month
            $thisMonth = \App\Models\Tagihan::whereMonth('tagihans.updated_at', Carbon::now()->month)
                ->whereYear('tagihans.updated_at', Carbon::now()->year)
                ->where('tagihans.status_pembayaran', 'lunas')
                ->join('pakets', 'tagihans.paket_id', '=', 'pakets.id')
                ->sum('pakets.harga');
            
            $lastMonth = \App\Models\Tagihan::whereMonth('tagihans.updated_at', Carbon::now()->subMonth()->month)
                ->whereYear('tagihans.updated_at', Carbon::now()->subMonth()->year)
                ->where('tagihans.status_pembayaran', 'lunas')
                ->join('pakets', 'tagihans.paket_id', '=', 'pakets.id')
                ->sum('pakets.harga');
            
            $growth = $lastMonth > 0 ? round((($thisMonth - $lastMonth) / $lastMonth) * 100, 2) : 0;
            $growthIcon = $growth >= 0 ? '📈' : '📉';
            
            // Prediction for end of month
            $daysInMonth = Carbon::now()->daysInMonth;
            $currentDay = Carbon::now()->day;
            $predictedMonth = ($thisMonth / $currentDay) * $daysInMonth;
            
            $message .= "📊 *7 HARI TERAKHIR*\n";
            $message .= "  • Total: Rp " . number_format($totalLast7, 0, ',', '.') . "\n";
            $message .= "  • Rata-rata/hari: Rp " . number_format($avgDaily, 0, ',', '.') . "\n\n";
            
            $message .= "📅 *BULAN INI vs LALU*\n";
            $message .= "  • Bulan ini: Rp " . number_format($thisMonth, 0, ',', '.') . "\n";
            $message .= "  • Bulan lalu: Rp " . number_format($lastMonth, 0, ',', '.') . "\n";
            $message .= "  • Growth: {$growthIcon} {$growth}%\n\n";
            
            $message .= "🔮 *PREDIKSI*\n";
            $message .= "  • Prediksi akhir bulan: Rp " . number_format($predictedMonth, 0, ',', '.') . "\n";
            
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown'
            ]);
            
            $this->info("Sent revenue analytics to $chatId");
            
        } catch (\Exception $e) {
            $this->error("Revenue analytics error: " . $e->getMessage());
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "❌ Error: " . $e->getMessage()
            ]);
        }
    }

    private function sendTagihanToday($chatId)
    {
        try {
            $message = "📋 *TAGIHAN HARI INI*\n";
            $message .= "━━━━━━━━━━━━━━━━━━━━\n\n";
            
            // Due today
            $dueToday = \App\Models\Tagihan::whereDate('tanggal_berakhir', Carbon::today())
                ->where('status_pembayaran', 'belum bayar')
                ->with('pelanggan', 'paket')
                ->get();
            
            // Overdue
            $overdue = \App\Models\Tagihan::where('tanggal_berakhir', '<', Carbon::today())
                ->where('status_pembayaran', 'belum bayar')
                ->count();
            
            // Paid today
            $paidToday = \App\Models\Tagihan::whereDate('tagihans.updated_at', Carbon::today())
                ->where('tagihans.status_pembayaran', 'lunas')
                ->count();
            
            $totalDue = 0;
            $message .= "⏰ *JATUH TEMPO HARI INI: {$dueToday->count()}*\n";
            
            if ($dueToday->count() > 0) {
                foreach ($dueToday->take(5) as $tagihan) {
                    $nama = $tagihan->pelanggan->nama ?? 'N/A';
                    $harga = $tagihan->paket->harga ?? 0;
                    $totalDue += $harga;
                    $message .= "  • {$nama}: Rp " . number_format($harga, 0, ',', '.') . "\n";
                }
                if ($dueToday->count() > 5) {
                    $message .= "  ... dan " . ($dueToday->count() - 5) . " lainnya\n";
                }
                $message .= "\n💰 Total: Rp " . number_format($totalDue, 0, ',', '.') . "\n\n";
            } else {
                $message .= "  ✅ Tidak ada\n\n";
            }
            
            $message .= "📊 *STATISTIK*\n";
            $message .= "  • Overdue: {$overdue}\n";
            $message .= "  • Dibayar hari ini: {$paidToday}\n";
            
            if ($dueToday->count() > 10) {
                $message .= "\n⚠️ *WARNING: Banyak tagihan jatuh tempo!*";
            }
            
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown'
            ]);
            
            $this->info("Sent tagihan today to $chatId");
            
        } catch (\Exception $e) {
            $this->error("Tagihan today error: " . $e->getMessage());
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "❌ Error: " . $e->getMessage()
            ]);
        }
    }

    private function sendCustomersWithoutCurrentMonthBill($chatId)
    {
        try {
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;
            $periodeLabel = Carbon::now()->format('m/Y');

            $customers = \App\Models\Pelanggan::query()
                ->where('status', 'approve')
                ->whereNotNull('paket_id')
                ->whereDoesntHave('tagihans', function ($q) use ($currentMonth, $currentYear) {
                    // Acuan periode tagihan berdasarkan tanggal mulai (bukan created_at).
                    $q->whereMonth('tanggal_mulai', $currentMonth)
                        ->whereYear('tanggal_mulai', $currentYear);
                })
                ->orderBy('nama_lengkap')
                ->get(['nomer_id', 'nama_lengkap', 'no_whatsapp']);

            if ($customers->isEmpty()) {
                $message = "✅ Semua pelanggan aktif sudah memiliki tagihan untuk periode {$periodeLabel}.";
                $this->telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => $message,
                ]);
            } else {
                $header = "Pelanggan Belum Memiliki Tagihan ({$periodeLabel})\n";
                $header .= "Total: {$customers->count()} pelanggan";

                $lines = [];
                foreach ($customers as $index => $customer) {
                    $nomerId = $customer->nomer_id ?: '-';
                    $nama = $customer->nama_lengkap ?: '-';
                    $wa = $customer->no_whatsapp ?: '-';
                    $lines[] = ($index + 1) . ". {$nomerId} - {$nama} ({$wa})";
                }

                $maxChars = 3500;
                $chunks = [];
                $currentChunk = '';

                foreach ($lines as $line) {
                    $candidate = $currentChunk === '' ? $line : $currentChunk . "\n" . $line;
                    if (mb_strlen($candidate) > $maxChars) {
                        $chunks[] = $currentChunk;
                        $currentChunk = $line;
                    } else {
                        $currentChunk = $candidate;
                    }
                }

                if ($currentChunk !== '') {
                    $chunks[] = $currentChunk;
                }

                $this->telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => $header,
                ]);

                $totalChunks = count($chunks);
                foreach ($chunks as $idx => $chunk) {
                    $chunkHeader = "Daftar " . ($idx + 1) . "/{$totalChunks}\n";
                    $this->telegram->sendMessage([
                        'chat_id' => $chatId,
                        'text' => $chunkHeader . $chunk,
                    ]);
                }
            }

            $this->info("Sent customers without current month bill to $chatId");
        } catch (\Exception $e) {
            $this->error("Failed to get customers without current month bill: " . $e->getMessage());
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "❌ Error saat mengecek pelanggan tanpa tagihan bulan ini: " . $e->getMessage()
            ]);
        }
    }

    private function sendPredictiveAnalytics($chatId)
    {
        try {
            $message = "🔮 *PREDICTIVE ANALYTICS*\n";
            $message .= "━━━━━━━━━━━━━━━━━━━━\n\n";
            
            // Churn Risk Analysis
            $inactiveCustomers = \App\Models\Pelanggan::whereHas('loginStatus', function($q) {
                $q->where('is_active', false)
                    ->where('logged_in_at', '<', Carbon::now()->subDays(30));
            })->count();
            
            $totalCustomers = \App\Models\Pelanggan::count();
            $churnRate = round(($inactiveCustomers / max($totalCustomers, 1)) * 100, 2);
            
            // Payment Pattern Analysis
            $avgPaymentDelay = \App\Models\Tagihan::where('tagihans.status_pembayaran', 'lunas')
                ->whereNotNull('tagihans.updated_at')
                ->whereNotNull('tagihans.tanggal_berakhir')
                ->selectRaw('AVG(DATEDIFF(tagihans.updated_at, tagihans.tanggal_berakhir)) as avg_delay')
                ->first();
            
            $delay = round($avgPaymentDelay->avg_delay ?? 0, 1);
            
            // Growth Prediction
            $thisMonthCustomers = \App\Models\Pelanggan::whereMonth('created_at', Carbon::now()->month)->count();
            $lastMonthCustomers = \App\Models\Pelanggan::whereMonth('created_at', Carbon::now()->subMonth()->month)->count();
            $customerGrowth = $lastMonthCustomers > 0 ? round((($thisMonthCustomers - $lastMonthCustomers) / $lastMonthCustomers) * 100, 2) : 0;
            
            $message .= "👥 *CUSTOMER CHURN*\n";
            $message .= "  • Inactive >30 hari: {$inactiveCustomers}\n";
            $message .= "  • Churn Rate: {$churnRate}%\n";
            if ($churnRate > 15) {
                $message .= "  ⚠️ *WARNING: Churn rate tinggi!*\n";
            }
            $message .= "\n";
            
            $message .= "💳 *PAYMENT BEHAVIOR*\n";
            $message .= "  • Rata-rata delay: {$delay} hari\n";
            if ($delay > 5) {
                $message .= "  ⚠️ *INFO: Banyak pembayaran terlambat*\n";
            }
            $message .= "\n";
            
            $message .= "📈 *GROWTH TREND*\n";
            $message .= "  • Pelanggan baru bulan ini: {$thisMonthCustomers}\n";
            $message .= "  • Growth rate: {$customerGrowth}%\n";
            
            // Predictions
            $message .= "\n🎯 *REKOMENDASI*\n";
            if ($churnRate > 15) {
                $message .= "  • Segera follow-up pelanggan tidak aktif\n";
            }
            if ($delay > 5) {
                $message .= "  • Pertimbangkan reminder otomatis\n";
            }
            if ($customerGrowth < 0) {
                $message .= "  • Tingkatkan marketing & retention\n";
            }
            
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown'
            ]);
            
            $this->info("Sent predictive analytics to $chatId");
            
        } catch (\Exception $e) {
            $this->error("Predictive analytics error: " . $e->getMessage());
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "❌ Error: " . $e->getMessage()
            ]);
        }
    }

    private function sendDatabasePerformance($chatId)
    {
        try {
            $message = "🗄️ *DATABASE PERFORMANCE*\n";
            $message .= "━━━━━━━━━━━━━━━━━━━━\n\n";
            
            // Table sizes
            $tables = [
                'tagihans' => \App\Models\Tagihan::count(),
                'pelanggans' => \App\Models\Pelanggan::count(),
                'users' => User::count(),
                'messages' => \App\Models\Message::count(),
            ];
            
            // Database size
            $dbSize = DB::select("SELECT 
                SUM(data_length + index_length) as size 
                FROM information_schema.TABLES 
                WHERE table_schema = DATABASE()");
            
            $size = isset($dbSize[0]->size) ? $this->formatBytes($dbSize[0]->size) : 'N/A';
            
            // Connection count
            $connections = DB::select("SHOW STATUS LIKE 'Threads_connected'");
            $connCount = $connections[0]->Value ?? 'N/A';
            
            $message .= "📊 *DATABASE INFO*\n";
            $message .= "  • Total Size: {$size}\n";
            $message .= "  • Connections: {$connCount}\n\n";
            
            $message .= "📋 *TABLE RECORDS*\n";
            foreach ($tables as $table => $count) {
                $message .= "  • {$table}: " . number_format($count) . "\n";
            }
            
            // Performance tips
            $totalRecords = array_sum($tables);
            if ($totalRecords > 100000) {
                $message .= "\n⚠️ *INFO: Database besar, pertimbangkan archiving*";
            }
            
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown'
            ]);
            
            $this->info("Sent database performance to $chatId");
            
        } catch (\Exception $e) {
            $this->error("Database performance error: " . $e->getMessage());
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "❌ Error: " . $e->getMessage()
            ]);
        }
    }

    private function sendErrorAnalytics($chatId)
    {
        try {
            $logPath = storage_path('logs/laravel.log');
            
            if (!file_exists($logPath)) {
                $this->telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => "✅ Tidak ada error log ditemukan."
                ]);
                return;
            }
            
            $message = "🐛 *ERROR ANALYTICS*\n";
            $message .= "━━━━━━━━━━━━━━━━━━━━\n\n";
            
            // Read last 50 lines of log
            $lines = array_slice(file($logPath), -50);
            $errors = [];
            $warnings = [];
            
            foreach ($lines as $line) {
                if (strpos($line, 'ERROR') !== false) {
                    $errors[] = $line;
                } elseif (strpos($line, 'WARNING') !== false) {
                    $warnings[] = $line;
                }
            }
            
            $message .= "📊 *LAST 50 LINES*\n";
            $message .= "  • Errors: " . count($errors) . "\n";
            $message .= "  • Warnings: " . count($warnings) . "\n\n";
            
            if (count($errors) > 0) {
                $message .= "🔴 *LATEST ERRORS* (max 3)\n";
                foreach (array_slice($errors, -3) as $error) {
                    $shortError = substr($error, 0, 100);
                    $message .= "  • " . trim($shortError) . "...\n";
                }
            } else {
                $message .= "✅ *No recent errors*\n";
            }
            
            if (count($errors) > 10) {
                $message .= "\n⚠️ *WARNING: Banyak error terdeteksi!*";
            }
            
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown'
            ]);
            
            $this->info("Sent error analytics to $chatId");
            
        } catch (\Exception $e) {
            $this->error("Error analytics error: " . $e->getMessage());
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "❌ Error: " . $e->getMessage()
            ]);
        }
    }

    private function sendCustomerInsights($chatId)
    {
        try {
            $message = "👥 *CUSTOMER INSIGHTS*\n";
            $message .= "━━━━━━━━━━━━━━━━━━━━\n\n";
            
            // New customers this week
            $newThisWeek = \App\Models\Pelanggan::whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])->count();
            
            // Active users today
            $activeToday = \App\Models\Pelanggan::whereHas('loginStatus', function($q) {
                $q->where('logged_in_at', '>=', Carbon::today());
            })->count();
            
            // Top paying customers
            $topCustomers = \App\Models\Tagihan::where('status_pembayaran', 'lunas')
                ->select('pelanggan_id', DB::raw('COUNT(*) as total_payments'))
                ->groupBy('pelanggan_id')
                ->orderBy('total_payments', 'DESC')
                ->limit(3)
                ->with('pelanggan')
                ->get();
            
            // Segmentation
            $withJMK = \App\Models\Pelanggan::where('nomer_id', 'like', '%JMK%')->count();
            $withoutJMK = \App\Models\Pelanggan::where('nomer_id', 'not like', '%JMK%')->count();
            
            $message .= "📈 *GROWTH*\n";
            $message .= "  • Pelanggan baru minggu ini: {$newThisWeek}\n";
            $message .= "  • Aktif hari ini: {$activeToday}\n\n";
            
            $message .= "🏆 *TOP CUSTOMERS*\n";
            foreach ($topCustomers as $idx => $customer) {
                $nama = $customer->pelanggan->nama ?? 'N/A';
                $total = $customer->total_payments;
                $message .= "  " . ($idx + 1) . ". {$nama}: {$total}x bayar\n";
            }
            $message .= "\n";
            
            $message .= "📊 *SEGMENTATION*\n";
            $message .= "  • JMK: {$withJMK}\n";
            $message .= "  • Non-JMK: {$withoutJMK}\n";
            
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown'
            ]);
            
            $this->info("Sent customer insights to $chatId");
            
        } catch (\Exception $e) {
            $this->error("Customer insights error: " . $e->getMessage());
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "❌ Error: " . $e->getMessage()
            ]);
        }
    }

    private function sendSmartAlerts($chatId)
    {
        try {
            $message = "🚨 *SMART ALERTS*\n";
            $message .= "━━━━━━━━━━━━━━━━━━━━\n\n";
            
            $alerts = [];
            $criticalCount = 0;
            $warningCount = 0;
            
            // Check disk space
            $diskData = $this->getDiskUsage();
            if ($diskData['percentage'] > 90) {
                $alerts[] = ['level' => 'critical', 'msg' => "Disk space hampir penuh ({$diskData['percentage']}%)"];
                $criticalCount++;
            } elseif ($diskData['percentage'] > 80) {
                $alerts[] = ['level' => 'warning', 'msg' => "Disk space tinggi ({$diskData['percentage']}%)"];
                $warningCount++;
            }
            
            // Check overdue bills
            $overdue = \App\Models\Tagihan::where('tanggal_berakhir', '<', Carbon::today())
                ->where('status_pembayaran', 'belum bayar')
                ->count();
            if ($overdue > 50) {
                $alerts[] = ['level' => 'critical', 'msg' => "Banyak tagihan overdue ({$overdue})"];
                $criticalCount++;
            } elseif ($overdue > 20) {
                $alerts[] = ['level' => 'warning', 'msg' => "Tagihan overdue cukup banyak ({$overdue})"];
                $warningCount++;
            }
            
            // Check inactive customers
            $inactive = \App\Models\Pelanggan::whereHas('loginStatus', function($q) {
                $q->where('is_active', false)
                    ->where('logged_in_at', '<', Carbon::now()->subDays(30));
            })->count();
            
            if ($inactive > 100) {
                $alerts[] = ['level' => 'warning', 'msg' => "Banyak pelanggan tidak aktif >30 hari ({$inactive})"];
                $warningCount++;
            }
            
            // Check system health
            $load = sys_getloadavg();
            $cpuCores = $this->getCpuCores();
            $loadPercentage = round(($load[0] / max($cpuCores, 1)) * 100, 2);
            
            if ($loadPercentage > 80) {
                $alerts[] = ['level' => 'critical', 'msg' => "CPU load sangat tinggi ({$loadPercentage}%)"];
                $criticalCount++;
            }
            
            // Check database connection
            try {
                DB::connection()->getPdo();
            } catch (\Exception $e) {
                $alerts[] = ['level' => 'critical', 'msg' => "Database tidak terhubung!"];
                $criticalCount++;
            }
            
            $message .= "📊 *SUMMARY*\n";
            $message .= "  • 🔴 Critical: {$criticalCount}\n";
            $message .= "  • 🟡 Warning: {$warningCount}\n\n";
            
            if (count($alerts) > 0) {
                $message .= "⚠️ *ALERTS*\n";
                foreach ($alerts as $alert) {
                    $icon = $alert['level'] == 'critical' ? '🔴' : '🟡';
                    $message .= "{$icon} {$alert['msg']}\n";
                }
            } else {
                $message .= "✅ *Semua sistem normal, tidak ada alert*";
            }
            
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown'
            ]);
            
            $this->info("Sent smart alerts to $chatId");
            
        } catch (\Exception $e) {
            $this->error("Smart alerts error: " . $e->getMessage());
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => "❌ Error: " . $e->getMessage()
            ]);
        }
    }

    private function sendHelpMenu($chatId)
    {
        try {
            $message = "📚 *TELEGRAM BOT COMMANDS*\n";
            $message .= "━━━━━━━━━━━━━━━━━━━━\n\n";
            
            $message .= "🎯 *DASHBOARD & ANALYTICS*\n";
            $message .= "/dashboard - Business dashboard\n";
            $message .= "/revenue - Revenue analytics\n";
            $message .= "/predict - Predictive analytics\n";
            $message .= "/insights - Customer insights\n\n";
            
            $message .= "📋 *BILLING & CUSTOMERS*\n";
            $message .= "/stats - Customer statistics\n";
            $message .= "/progres - Statistik progres pelanggan\n";
            $message .= "/tagihan - Tagihan hari ini\n";
            $message .= "/belum-tagihan - Belum ada tagihan bulan ini\n";
            $message .= "/chat - Chat belum dibalas\n";
            $message .= "/duplikat - Nomer ID duplikat\n\n";
            
            $message .= "🖥️ *SYSTEM MONITORING*\n";
            $message .= "/status - System status\n";
            $message .= "/health - Server health\n";
            $message .= "/speed - Internet speed\n";
            $message .= "/db-perf - Database performance\n";
            $message .= "/errors - Error analytics\n\n";
            
            $message .= "🚨 *ALERTS*\n";
            $message .= "/alerts - Smart alerts\n\n";
            
            $message .= "💡 *Gunakan command untuk monitoring real-time!*";
            
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown'
            ]);
            
            $this->info("Sent help menu to $chatId");
            
        } catch (\Exception $e) {
            $this->error("Help menu error: " . $e->getMessage());
        }
    }

    private function calculateHealthScore()
    {
        $score = 100;
        
        // Check CPU
        $load = sys_getloadavg();
        $cpuCores = $this->getCpuCores();
        $loadPercentage = round(($load[0] / max($cpuCores, 1)) * 100, 2);
        if ($loadPercentage > 80) $score -= 30;
        elseif ($loadPercentage > 60) $score -= 15;
        
        // Check Memory
        $memoryData = $this->getMemoryUsage();
        if ($memoryData['percentage'] > 85) $score -= 25;
        elseif ($memoryData['percentage'] > 70) $score -= 10;
        
        // Check Disk
        $diskData = $this->getDiskUsage();
        if ($diskData['percentage'] > 90) $score -= 25;
        elseif ($diskData['percentage'] > 80) $score -= 10;
        
        // Check Database
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            $score -= 40;
        }
        
        return max($score, 0);
    }
}
