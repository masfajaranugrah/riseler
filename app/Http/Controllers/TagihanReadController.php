<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class TagihanReadController extends Controller
{
    /**
     * Halaman status baca tagihan.
     */
    public function index()
    {
        return view('content.apps.Tagihan.tagihan-read');
    }

    /**
     * Data JSON status baca tagihan per tahun.
     */
    public function getDataJson(Request $request)
    {
        try {
            $year = $request->input('year', Carbon::now()->year);
            $search = $request->input('search');
            $perPage = 40; // Batasi 40 per halaman
            $hasReadAtColumn = Schema::hasColumn('tagihans', 'read_at');

            // Base query OPTIMIZED: Pelanggan JMK-GK yang punya tagihan di tahun terkait menggunakan JOIN (jauh lebih cepat)
            $query = Pelanggan::select('pelanggans.*')
                ->join('tagihans', 'pelanggans.id', '=', 'tagihans.pelanggan_id')
                ->where('pelanggans.nomer_id', 'LIKE', '%JMK-GK%')
                ->whereYear('tagihans.tanggal_mulai', $year)
                ->distinct();

            // Terapkan pencarian jika ada
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('pelanggans.nama_lengkap', 'like', "%{$search}%")
                      ->orWhere('pelanggans.nomer_id', 'like', "%{$search}%")
                      ->orWhere('pelanggans.no_whatsapp', 'like', "%{$search}%");
                });
            }

            // Paginasi pelanggan (karena pakai distinct dan ada relasi, with tags di bawah)
            $pelanggans = $query->with(['tagihans' => function ($q) use ($year) {
                $q->whereYear('tanggal_mulai', $year)->orderBy('tanggal_mulai', 'asc');
            }])->paginate($perPage);

            // Mapping hasil paginasi
            $mappedPelanggans = $pelanggans->map(function ($pelanggan) use ($hasReadAtColumn) {
                $tagihansMatrix = [];
                
                for ($i = 1; $i <= 12; $i++) {
                    $tagihansMatrix[$i] = [
                        'ada' => false,
                        'is_read' => false,
                        'read_at' => null,
                        'status_pembayaran' => null
                    ];
                }
                
                foreach ($pelanggan->tagihans as $tagihan) {
                    $month = Carbon::parse($tagihan->tanggal_mulai)->month;
                    $readAt = $hasReadAtColumn ? ($tagihan->read_at ?? null) : null;
                    $currentMonthData = $tagihansMatrix[$month];
                    $formattedReadAt = $readAt ? Carbon::parse($readAt)->format('d M H:i') : null;

                    $tagihansMatrix[$month] = [
                        'ada' => true,
                        // Jika ada lebih dari 1 tagihan di bulan yang sama, tampilkan centang jika salah satu sudah dibaca.
                        'is_read' => $currentMonthData['is_read'] || !is_null($readAt),
                        'read_at' => $currentMonthData['read_at'] ?? $formattedReadAt,
                        'status_pembayaran' => $tagihan->status_pembayaran
                    ];
                }

                return [
                    'id' => $pelanggan->id,
                    'nomer_id' => $pelanggan->nomer_id ?? '-',
                    'nama_lengkap' => $pelanggan->nama_lengkap ?? '-',
                    'no_whatsapp' => $pelanggan->no_whatsapp ?? '-',
                    'tagihans_matrix' => $tagihansMatrix
                ];
            });

            // Statistik khusus total SEMUA pelanggan JMK-GK (Sesuai permintaan)
            $totalPelanggan = Pelanggan::where('nomer_id', 'LIKE', '%JMK-GK%')->count();

            $sudahBaca = 0;
            if ($hasReadAtColumn) {
                // Optimalisasi join untuk hitung pelanggan yang sudah baca (distinct id)
                $sudahBaca = Pelanggan::join('tagihans', 'pelanggans.id', '=', 'tagihans.pelanggan_id')
                    ->where('pelanggans.nomer_id', 'LIKE', '%JMK-GK%')
                    ->whereYear('tagihans.tanggal_mulai', $year)
                    ->whereNotNull('tagihans.read_at')
                    ->distinct('pelanggans.id')
                    ->count('pelanggans.id');
            }

            $stats = [
                'total' => $totalPelanggan,
                'sudah_baca' => $sudahBaca,
                'belum_baca' => $totalPelanggan - $sudahBaca,
            ];

            return response()->json([
                'status' => true,
                'data' => [
                    'pelanggans' => $mappedPelanggans,
                    'pagination' => [
                        'current_page' => $pelanggans->currentPage(),
                        'last_page' => $pelanggans->lastPage(),
                        'total' => $pelanggans->total(),
                        'per_page' => $pelanggans->perPage(),
                        'from' => $pelanggans->firstItem() ?? 0,
                        'to' => $pelanggans->lastItem() ?? 0
                    ],
                    'statistics' => $stats,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('TagihanReadController@getDataJson failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Gagal mengambil data status baca.',
            ], 500);
        }
    }
}
