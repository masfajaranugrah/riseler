<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PushNotificationController extends Controller
{
    public function index()
    {
        try {
            $tagihans = Tagihan::with(['pelanggan', 'paket'])
                ->where('status_pembayaran', 'belum bayar')
                ->orderBy('created_at', 'desc')
                ->get();

            $tagihans = $tagihans->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nama_lengkap' => $item->pelanggan->nama_lengkap ?? '-',
                    'nomer_id' => $item->pelanggan->nomer_id ?? '-',
                    'no_whatsapp' => $item->pelanggan->no_whatsapp ?? '08xxxxxxxxxx',
                    'paket' => [
                        'id' => $item->paket->id ?? null,
                        'nama_paket' => $item->paket->nama_paket ?? '-',
                        'harga' => $item->paket->harga ?? 0,
                    ],
                    'tanggal_mulai' => $item->tanggal_mulai ?? null,
                    'tanggal_berakhir' => $item->tanggal_berakhir ?? null,
                    'status_pembayaran' => $item->status_pembayaran ?? 'belum bayar',
                    'catatan' => $item->catatan ?? '-',
                ];
            });

            return view('content.apps.PushNotification.push', compact('tagihans'));
        } catch (\Exception $e) {
            Log::error('Error loading push notification page: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memuat halaman');
        }
    }

    /**
     * Kirim push notification tagihan ke pelanggan
     */
    public function broadcast(Request $request)
    {
        try {
            $tagihanIds = $request->input('tagihan_ids', []);
            $sentCount = 0;
            $ignoredCount = 0;
            $failedCount = 0;

            Log::info('Starting broadcast notification', ['tagihan_ids' => $tagihanIds]);

            if (empty($tagihanIds)) {
                return response()->json([
                    'success' => false,
                    'sent' => 0,
                    'ignored' => 0,
                    'failed' => 0,
                    'message' => 'Tidak ada tagihan yang dipilih'
                ]);
            }

            $tagihans = Tagihan::with('pelanggan')
                ->whereIn('id', $tagihanIds)
                ->where('status_pembayaran', 'belum bayar')
                ->get();

            foreach ($tagihans as $tagihan) {
                try {
                    $pelanggan = $tagihan->pelanggan;

                    if (!$pelanggan || empty($pelanggan->webpushr_sid)) {
                        $ignoredCount++;
                        continue;
                    }

                    $result = $this->sendWebpushrNotification([
                        'title' => 'Tagihan Belum Dibayar',
                        'message' => "Halo {$pelanggan->nama_lengkap}, tagihan Anda akan jatuh tempo pada " . 
                                    ($tagihan->tanggal_berakhir ?? 'segera') . ". Mohon segera lakukan pembayaran.",
                        'target_url' => url('/dashboard/customer/tagihan'),
                        'sid' => $pelanggan->webpushr_sid,
                    ]);

                    if ($result['success']) {
                        $sentCount++;
                    } else {
                        $failedCount++;
                    }

                } catch (\Exception $e) {
                    $failedCount++;
                    Log::error('Error sending notification', ['error' => $e->getMessage()]);
                    continue;
                }
            }

            return response()->json([
                'success' => true,
                'sent' => $sentCount,
                'ignored' => $ignoredCount,
                'failed' => $failedCount,
                'total' => count($tagihanIds),
                'message' => $sentCount > 0 
                    ? "Berhasil mengirim {$sentCount} notifikasi" 
                    : 'Tidak ada notifikasi yang terkirim'
            ]);

        } catch (\Exception $e) {
            Log::error('Broadcast error: ' . $e->getMessage());
            return response()->json([
                'success' => true,
                'sent' => 0,
                'ignored' => 0,
                'failed' => 0,
                'message' => 'Terjadi kesalahan saat mengirim notifikasi'
            ]);
        }
    }

    /**
     * Kirim broadcast info/iklan ke semua pelanggan
     */
    public function broadcastInfo(Request $request)
    {
        try {
            $message = $request->input('message', '');

            if (empty($message)) {
                return response()->json([
                    'success' => false,
                    'sent' => 0,
                    'ignored' => 0,
                    'message' => 'Pesan tidak boleh kosong'
                ]);
            }

            $pelanggans = Pelanggan::whereNotNull('webpushr_sid')
                ->where('webpushr_sid', '!=', '')
                ->get();

            if ($pelanggans->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'sent' => 0,
                    'ignored' => 0,
                    'message' => 'Tidak ada pelanggan dengan SID yang valid'
                ]);
            }

            $sentCount = 0;
            $failedCount = 0;

            foreach ($pelanggans as $pelanggan) {
                try {
                    $result = $this->sendWebpushrNotification([
                        'title' => 'Info Penting',
                        'message' => $message,
                        'target_url' => url('/dashboard/customer/tagihan/home'),
                        'sid' => $pelanggan->webpushr_sid,
                    ]);

                    if ($result['success']) {
                        $sentCount++;
                    } else {
                        $failedCount++;
                    }

                } catch (\Exception $e) {
                    $failedCount++;
                    continue;
                }
            }

            return response()->json([
                'success' => true,
                'sent' => $sentCount,
                'ignored' => $failedCount,
                'total' => $pelanggans->count(),
                'message' => $sentCount > 0 
                    ? "Berhasil mengirim {$sentCount} notifikasi info" 
                    : 'Tidak ada notifikasi yang terkirim'
            ]);

        } catch (\Exception $e) {
            Log::error('Broadcast info error: ' . $e->getMessage());
            return response()->json([
                'success' => true,
                'sent' => 0,
                'ignored' => 0,
                'message' => 'Terjadi kesalahan saat mengirim notifikasi'
            ]);
        }
    }

    /**
     * Helper function untuk mengirim notifikasi via WebPushr API
     */
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

    /**
     * Check apakah ada tagihan pending untuk pelanggan
     */
    public function check($nomer_id)
    {
        try {
            $pending = Tagihan::whereHas('pelanggan', function($q) use ($nomer_id) {
                    $q->where('nomer_id', $nomer_id);
                })
                ->where('status_pembayaran', 'belum bayar')
                ->exists();

            return response()->json(['has_notification' => $pending]);
        } catch (\Exception $e) {
            return response()->json(['has_notification' => false]);
        }
    }

    /**
     * Check apakah ada broadcast info untuk ditampilkan
     */
    public function checkBroadcastInfo($nomer_id)
    {
        try {
            $pelanggan = Pelanggan::where('nomer_id', $nomer_id)->first();
            
            if (!$pelanggan) {
                return response()->json(['has_info' => false, 'info' => null]);
            }

            // Array info/iklan yang tersedia
            $availableInfos = [
                [
                    'id' => 1,
                    'title' => '?? Promo Spesial Bulan Ini!',
                    'message' => 'Dapatkan diskon 30% untuk upgrade paket internet! Buruan sebelum kehabisan.',
                    'action_url' => '/dashboard/customer/tagihan',
                    'features' => [
                        ['icon' => 'bi-percent', 'text' => 'Diskon 30%'],
                        ['icon' => 'bi-clock-history', 'text' => 'Terbatas']
                    ]
                ],
                [
                    'id' => 2,
                    'title' => '?? Maintenance Terjadwal',
                    'message' => 'Sistem akan maintenance pada Minggu, 15 Desember 2025 pukul 01:00 - 05:00 WIB.',
                    'features' => [
                        ['icon' => 'bi-tools', 'text' => 'Maintenance'],
                        ['icon' => 'bi-calendar-check', 'text' => '15 Des 2025']
                    ]
                ],
                [
                    'id' => 3,
                    'title' => '?? Cashback Spesial!',
                    'message' => 'Bayar tagihan tepat waktu dan dapatkan cashback hingga Rp 50.000!',
                    'features' => [
                        ['icon' => 'bi-cash-coin', 'text' => 'Cashback'],
                        ['icon' => 'bi-gift', 'text' => 'Hingga 50rb']
                    ]
                ],
                [
                    'id' => 4,
                    'title' => '? Upgrade Kecepatan',
                    'message' => 'Nikmati kecepatan internet 2x lebih cepat dengan harga spesial!',
                    'features' => [
                        ['icon' => 'bi-lightning-charge-fill', 'text' => '2x Lebih Cepat'],
                        ['icon' => 'bi-tag-fill', 'text' => 'Harga Spesial']
                    ]
                ]
            ];

            // Pilih info random
            $selectedInfo = $availableInfos[array_rand($availableInfos)];

            return response()->json([
                'has_info' => true,
                'info' => $selectedInfo
            ]);

        } catch (\Exception $e) {
            Log::error('Check broadcast info error: ' . $e->getMessage());
            return response()->json(['has_info' => false, 'info' => null]);
        }
    }
}
