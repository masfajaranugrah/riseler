<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use App\Models\Pelanggan;
use App\Jobs\SendTagihanPushJob;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class PushNotificationController extends Controller
{
    public function index(Request $request)
    {
        try {
            $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
            $endOfMonth = Carbon::now()->endOfMonth()->toDateString();

            $query = Tagihan::with(['pelanggan', 'paket'])
                ->select('tagihans.*')
                ->join('pelanggans', 'tagihans.pelanggan_id', '=', 'pelanggans.id')
                ->whereRaw('LOWER(tagihans.status_pembayaran) = ?', ['belum bayar'])
                ->where('pelanggans.nomer_id', 'LIKE', '%JMK-GK%')
                ->orderBy('tagihans.created_at', 'desc');

            // Search filter
            if ($request->filled('search')) {
                $search = trim($request->search);
                $query->where(function ($q) use ($search) {
                    $q->where('pelanggans.nama_lengkap', 'like', "%{$search}%")
                        ->orWhere('pelanggans.nomer_id', 'like', "%{$search}%")
                        ->orWhere('pelanggans.no_whatsapp', 'like', "%{$search}%");
                });
            }

            // Use Laravel pagination (40 per page)
            $tagihans = $query->paginate(40)->withQueryString()->through(function ($item) {
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

            // Get total count for "send to all" feature
            $totalTagihan = Tagihan::join('pelanggans', 'tagihans.pelanggan_id', '=', 'pelanggans.id')
                ->whereRaw('LOWER(tagihans.status_pembayaran) = ?', ['belum bayar'])
                ->where('pelanggans.nomer_id', 'LIKE', '%JMK-GK%')
                ->count();

            return view('content.apps.PushNotification.push', compact('tagihans', 'totalTagihan'));
        } catch (\Exception $e) {
            Log::error('Error loading push notification page: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memuat halaman');
        }
    }

    /**
     * Get all tagihan IDs for broadcast (AJAX endpoint)
     */
    public function getAllTagihanIds()
    {
        try {
            $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
            $endOfMonth = Carbon::now()->endOfMonth()->toDateString();

            $ids = Tagihan::join('pelanggans', 'tagihans.pelanggan_id', '=', 'pelanggans.id')
                ->whereRaw('LOWER(tagihans.status_pembayaran) = ?', ['belum bayar'])
                ->where('pelanggans.nomer_id', 'LIKE', '%JMK-GK%')
                ->where('pelanggans.status', 'approve')
                ->pluck('tagihans.id')
                ->toArray();

            return response()->json([
                'success' => true,
                'ids' => $ids,
                'total' => count($ids)
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting all tagihan IDs: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'ids' => [],
                'total' => 0,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get outstanding tagihan IDs for broadcast (AJAX endpoint)
     * Outstanding = belum bayar dan sudah lewat tanggal berakhir.
     */
    public function getOutstandingTagihanIds()
    {
        try {
            $startOfMonth = Carbon::now()->startOfMonth()->toDateString();

            $ids = Tagihan::join('pelanggans', 'tagihans.pelanggan_id', '=', 'pelanggans.id')
                ->where('tagihans.status_pembayaran', 'belum bayar')
                ->where('pelanggans.nomer_id', 'LIKE', '%JMK-GK%')
                ->where('pelanggans.status', 'approve')
                // Outstanding = bulan sebelum bulan ini (exclude bulan berjalan)
                ->whereDate('tagihans.tanggal_mulai', '<', $startOfMonth)
                ->pluck('tagihans.id')
                ->toArray();

            return response()->json([
                'success' => true,
                'ids' => $ids,
                'total' => count($ids)
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting outstanding tagihan IDs: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'ids' => [],
                'total' => 0,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kirim push notification tagihan ke pelanggan
     */
    public function broadcast(Request $request)
    {
        try {
            $tagihanIds = $request->input('tagihan_ids', []);
            $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
            $endOfMonth = Carbon::now()->endOfMonth()->toDateString();

            Log::info('[push_notification] broadcast requested', [
                'requested_total' => count($tagihanIds),
                'queue_connection' => config('queue.default'),
            ]);

            if (empty($tagihanIds)) {
                return response()->json([
                    'success' => false,
                    'queued' => false,
                    'message' => 'Tidak ada tagihan yang dipilih'
                ]);
            }

            // Guard: hanya tagihan yang belum bayar
            $allowedTagihanIds = Tagihan::query()
                ->whereIn('id', $tagihanIds)
                ->whereRaw('LOWER(status_pembayaran) = ?', ['belum bayar'])
                ->pluck('id')
                ->toArray();

            if (empty($allowedTagihanIds)) {
                return response()->json([
                    'success' => false,
                    'queued' => false,
                    'message' => 'Tidak ada tagihan bulan ini yang valid untuk dikirim',
                ]);
            }

            $dispatchResult = $this->dispatchTagihanPush($allowedTagihanIds, 'broadcast');

            return response()->json([
                'success' => true,
                'queued' => $dispatchResult['queued'],
                'mode' => $dispatchResult['mode'],
                'message' => $dispatchResult['queued']
                    ? 'Notifikasi sedang dikirim di background melalui queue'
                    : 'Notifikasi sedang diproses langsung (sync fallback karena queue tidak siap)',
                'total' => count($allowedTagihanIds),
            ]);

        } catch (\Exception $e) {
            Log::error('Broadcast error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'queued' => false,
                'message' => 'Terjadi kesalahan saat mengirim notifikasi'
            ]);
        }
    }

    /**
     * Kirim push notification khusus outstanding (bulan sebelum bulan ini)
     */
    public function broadcastOutstanding(Request $request)
    {
        try {
            $tagihanIds = $request->input('tagihan_ids', []);
            $startOfMonth = Carbon::now()->startOfMonth()->toDateString();

            Log::info('[push_notification] broadcast outstanding requested', [
                'requested_total' => count($tagihanIds),
                'queue_connection' => config('queue.default'),
            ]);

            if (empty($tagihanIds)) {
                return response()->json([
                    'success' => false,
                    'queued' => false,
                    'message' => 'Tidak ada tagihan outstanding yang dipilih'
                ]);
            }

            $allowedTagihanIds = Tagihan::query()
                ->whereIn('id', $tagihanIds)
                ->where('status_pembayaran', 'belum bayar')
                ->whereDate('tanggal_mulai', '<', $startOfMonth)
                ->pluck('id')
                ->toArray();

            if (empty($allowedTagihanIds)) {
                return response()->json([
                    'success' => false,
                    'queued' => false,
                    'message' => 'Tidak ada tagihan outstanding yang valid untuk dikirim',
                ]);
            }

            $dispatchResult = $this->dispatchTagihanPush($allowedTagihanIds, 'broadcast_outstanding');

            return response()->json([
                'success' => true,
                'queued' => $dispatchResult['queued'],
                'mode' => $dispatchResult['mode'],
                'message' => $dispatchResult['queued']
                    ? 'Notifikasi outstanding sedang dikirim di background melalui queue'
                    : 'Notifikasi outstanding sedang diproses langsung (sync fallback karena queue tidak siap)',
                'total' => count($allowedTagihanIds),
            ]);
        } catch (\Exception $e) {
            Log::error('Broadcast outstanding error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'queued' => false,
                'message' => 'Terjadi kesalahan saat mengirim notifikasi outstanding'
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

            Log::info('[push_notification] broadcast info requested', [
                'message_length' => mb_strlen((string) $message),
            ]);

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
                ->where('nomer_id', 'LIKE', '%JMK-GK%')
                ->where('status', 'approve')
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
                    Log::warning('[push_notification] broadcast info failed per pelanggan', [
                        'pelanggan_id' => $pelanggan->id,
                        'error' => $e->getMessage(),
                    ]);
                    continue;
                }
            }

            Log::info('[push_notification] broadcast info finished', [
                'total' => $pelanggans->count(),
                'sent' => $sentCount,
                'ignored' => $failedCount,
            ]);

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
     * Dispatch job push dengan fallback sync jika queue database belum siap.
     *
     * @param array<int|string> $allowedTagihanIds
     * @return array{queued:bool,mode:string}
     */
    private function dispatchTagihanPush(array $allowedTagihanIds, string $source): array
    {
        $queueConnection = (string) config('queue.default', 'database');
        $jobsTable = (string) config('queue.connections.database.table', 'jobs');

        try {
            if ($queueConnection === 'database' && !Schema::hasTable($jobsTable)) {
                Log::warning('[push_notification] queue jobs table missing, fallback to sync', [
                    'source' => $source,
                    'jobs_table' => $jobsTable,
                    'total' => count($allowedTagihanIds),
                ]);

                SendTagihanPushJob::dispatchSync($allowedTagihanIds);

                Log::info('[push_notification] sync fallback finished', [
                    'source' => $source,
                    'total' => count($allowedTagihanIds),
                ]);

                return ['queued' => false, 'mode' => 'sync_fallback'];
            }

            SendTagihanPushJob::dispatch($allowedTagihanIds);

            Log::info('[push_notification] queued successfully', [
                'source' => $source,
                'total' => count($allowedTagihanIds),
                'queue_connection' => $queueConnection,
            ]);

            return ['queued' => true, 'mode' => 'queue'];
        } catch (\Throwable $e) {
            Log::error('[push_notification] dispatch failed', [
                'source' => $source,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Check apakah ada tagihan pending untuk pelanggan
     * Returns: has_notification, total_tagihan, tunggakan_count, bulan_tunggakan
     */
    public function check($nomer_id)
    {
        try {
            $tagihans = Tagihan::whereHas('pelanggan', function ($q) use ($nomer_id) {
                $q->where('nomer_id', $nomer_id);
            })
                ->where('status_pembayaran', 'belum bayar')
                ->get();

            if ($tagihans->isEmpty()) {
                return response()->json(['has_notification' => false]);
            }

            $now = \Carbon\Carbon::now();

            // Hitung tunggakan (yang sudah lewat tanggal_berakhir)
            $tunggakan = $tagihans->filter(function ($t) use ($now) {
                return $t->tanggal_berakhir && \Carbon\Carbon::parse($t->tanggal_berakhir)->lt($now);
            });

            $tunggakanCount = $tunggakan->count();

            // Hitung bulan tunggakan terlama dari tanggal_berakhir paling awal
            $bulanTunggakan = 0;
            if ($tunggakanCount > 0) {
                $oldest = $tunggakan->sortBy('tanggal_berakhir')->first();
                $bulanTunggakan = (int) \Carbon\Carbon::parse($oldest->tanggal_berakhir)->diffInMonths($now);
                // Minimum 1 bulan jika ada tunggakan
                $bulanTunggakan = max(1, $bulanTunggakan);
            }

            return response()->json([
                'has_notification'  => true,
                'total_tagihan'     => $tagihans->count(),
                'tunggakan_count'   => $tunggakanCount,
                'bulan_tunggakan'   => $bulanTunggakan,
            ]);

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
