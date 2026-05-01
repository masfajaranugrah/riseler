<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Paket;
use App\Models\Pelanggan;
use App\Models\Tagihan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OutstandingController extends Controller
{
 
  

 


public function index(Request $request)
{
    // Pelanggan tidak di-load sekaligus lagi - diload via AJAX Select2
    // untuk performa lebih baik dengan 10k+ data
    $pelanggan = collect(); // Empty collection, data diload via AJAX
    $paket = Paket::all();

    // ? BUILD QUERY - JOIN OPTIMIZATION + FILTER JMK-GK + BELUM BAYAR SAJA
    $query = Tagihan::with(['pelanggan', 'paket'])
        ->select('tagihans.*')
        ->join('pelanggans', 'tagihans.pelanggan_id', '=', 'pelanggans.id')
        ->where('tagihans.status_pembayaran', 'belum bayar') // PASTIKAN HANYA BELUM BAYAR
        ->where('pelanggans.nomer_id', 'LIKE', '%JMK-GK%'); // PASTIKAN HANYA JMK-GK

    // ? FILTER OUTSTANDING: Hanya tagihan bulan sebelumnya (bukan bulan ini)
    $query->where(function($q) {
        $q->whereYear('tagihans.tanggal_berakhir', '<', now()->year)
          ->orWhere(function($subQ) {
              $subQ->whereYear('tagihans.tanggal_berakhir', '=', now()->year)
                   ->whereMonth('tagihans.tanggal_berakhir', '<', now()->month);
          });
    });

    // ? SEARCH FILTER
    if ($request->filled('search')) {
        $search = trim($request->search);
        $query->where(function ($q) use ($search) {
            $q->where('pelanggans.nama_lengkap', 'like', "%{$search}%")
                ->orWhere('pelanggans.nomer_id', 'like', "%{$search}%")
                ->orWhere('pelanggans.no_whatsapp', 'like', "%{$search}%")
                ->orWhere('pelanggans.no_telp', 'like', "%{$search}%")
                ->orWhere('pelanggans.alamat_jalan', 'like', "%{$search}%")
                ->orWhere('pelanggans.rt', 'like', "%{$search}%")
                ->orWhere('pelanggans.rw', 'like', "%{$search}%")
                ->orWhere('pelanggans.desa', 'like', "%{$search}%")
                ->orWhere('pelanggans.kecamatan', 'like', "%{$search}%")
                ->orWhere('pelanggans.kabupaten', 'like', "%{$search}%")
                ->orWhere('pelanggans.kode_pos', 'like', "%{$search}%");
        });
    }

    // ? PAGINATION
    $tagihans = $query->orderBy('tanggal_berakhir', 'desc')
        ->paginate(40, ['tagihans.*'])
        ->withQueryString()
        ->through(function ($item) {
            $pelanggan = $item->pelanggan;
            $paket = $item->paket;

            return [
                'id' => $item->id,
                'pelanggan_id' => $item->pelanggan_id,
                'nomer_id' => $pelanggan->nomer_id ?? '-',
                'nama_lengkap' => $pelanggan->nama_lengkap ?? '-',
                'alamat_jalan' => $pelanggan->alamat_jalan ?? '-',
                'rt' => $pelanggan->rt ?? '-',
                'rw' => $pelanggan->rw ?? '-',
                'desa' => $pelanggan->desa ?? '-',
                'kecamatan' => $pelanggan->kecamatan ?? '-',
                'kabupaten' => $pelanggan->kabupaten ?? '-',
                'provinsi' => $pelanggan->provinsi ?? '-',
                'kode_pos' => $pelanggan->kode_pos ?? '-',
                'paket' => [
                    'id' => $paket->id ?? null,
                    'nama_paket' => $paket->nama_paket ?? '-',
                    'harga' => $paket->harga ?? 0,
                    'kecepatan' => $paket->kecepatan ?? 0,
                    'masa_pembayaran' => $paket->masa_pembayaran ?? 0,
                    'durasi' => $paket->durasi ?? 0,
                ],
                'tanggal_mulai' => $item->tanggal_mulai,
                'tanggal_berakhir' => $item->tanggal_berakhir,
                'status_pembayaran' => $item->status_pembayaran ?? 'belum bayar',
                'tanggal_pembayaran' => $item->tanggal_pembayaran ?? '-',
                'bukti_pembayaran' => $item->bukti_pembayaran ?? '-',
                'no_whatsapp' => $pelanggan->no_whatsapp ?? '08xxxxxxxxxx',
                'catatan' => $item->catatan ?? '-',
            ];
        });

    // Statistik - update untuk outstanding saja (khusus JMK-GK)
    $totalCustomer = Pelanggan::where('status', 'approve')
        ->where('nomer_id', 'LIKE', '%JMK-GK%')
        ->count();

    $lunas = 0; // Sesuai permintaan: lunas jangan ditampilkan, jadi dinolkan saja untuk performa

    $belumLunas = Tagihan::join('pelanggans', 'tagihans.pelanggan_id', '=', 'pelanggans.id')
        ->where('tagihans.status_pembayaran', 'belum bayar')
        ->where('pelanggans.nomer_id', 'LIKE', '%JMK-GK%')
        ->where(function($q) {
            $q->whereYear('tagihans.tanggal_berakhir', '<', now()->year)
              ->orWhere(function($subQ) {
                  $subQ->whereYear('tagihans.tanggal_berakhir', '=', now()->year)
                       ->whereMonth('tagihans.tanggal_berakhir', '<', now()->month);
              });
        })->count();

    $totalPaket = $paket->count();

    return view('content.apps.Outstanding.tagihan', [
        'tagihans' => $tagihans,
        'pelanggan' => $pelanggan,
        'paket' => $paket,
        'totalCustomer' => $totalCustomer,
        'lunas' => $lunas,
        'belumLunas' => $belumLunas,
        'totalPaket' => $totalPaket,
    ]);
}



public function proses()
{
    // Ambil semua pelanggan & paket untuk dropdown modal
    $pelanggan = Pelanggan::all();
    $paket = Paket::all();

    // Query dengan pagination - 20 data per page (TANPA through/map)
    $tagihans = Tagihan::with(['pelanggan', 'paket'])
        ->where('status_pembayaran', 'proses_verifikasi')
        ->orderBy('created_at', 'desc')
        ->paginate(20); // HANYA INI SAJA, JANGAN PAKAI through() atau map()

    // Ambil list unik untuk filter dropdown
    $kabupatenList = $pelanggan->pluck('kabupaten')->unique();
    $kecamatanList = $pelanggan->pluck('kecamatan')->unique();

    // Statistik
    $totalCustomer = $pelanggan->count();
    $lunas = 0;
    $belumLunas = Tagihan::where('status_pembayaran', 'proses_verifikasi')->count();
    $totalPaket = $paket->count();

    return view('content.apps.Tagihan.proses-tagihan', compact(
        'tagihans',
        'pelanggan',
        'paket',
        'totalCustomer',
        'lunas',
        'belumLunas',
        'totalPaket',
        'kabupatenList',
        'kecamatanList'
    ));
}






public function lunas()
{
    // Ambil semua pelanggan & paket untuk dropdown modal
    $pelanggan = Pelanggan::all();
    $paket = Paket::all();

    // Ambil semua tagihan dengan status "lunas" beserta relasinya
    $tagihans = Tagihan::with(['pelanggan', 'paket'])
        ->where('status_pembayaran', 'lunas')
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($item) {
            $pelanggan = $item->pelanggan;
            $paket = $item->paket;


$kwitansiUrl = null;
if (!empty($item->kwitansi)) {
    $kwitansiUrl = $item->kwitansi;
}
            return [
                'id' => $item->id,
                'nomer_id' => $pelanggan->nomer_id ?? '-',
                'nama_lengkap' => $pelanggan->nama_lengkap ?? '-',
                'alamat_jalan' => $pelanggan->alamat_jalan ?? '-',
                'rt' => $pelanggan->rt ?? '-',
                'rw' => $pelanggan->rw ?? '-',
                'desa' => $pelanggan->desa ?? '-',
                'kecamatan' => $pelanggan->kecamatan ?? '-',
                'kabupaten' => $pelanggan->kabupaten ?? '-',
                'provinsi' => $pelanggan->provinsi ?? '-',
                'kode_pos' => $pelanggan->kode_pos ?? '-',
                'paket' => [
                    'id' => $paket->id ?? null,
                    'nama_paket' => $paket->nama_paket ?? '-',
                    'harga' => $paket->harga ?? 0,
                    'kecepatan' => $paket->kecepatan ?? 0,
                    'masa_pembayaran' => $paket->masa_pembayaran ?? 0,
                    'durasi' => $paket->durasi ?? 0,
                ],
                'tanggal_mulai' => $item->tanggal_mulai ?? null,
                'tanggal_berakhir' => $item->tanggal_berakhir ?? null,
                'status_pembayaran' => $item->status_pembayaran ?? 'belum bayar',
 		 'type_pembayaran' => $item->rekening->nama_bank ?? '-',

                'tanggal_pembayaran' => $item->tanggal_pembayaran ?? '-',
                'bukti_pembayaran' => $item->bukti_pembayaran ?? '-',
                'kwitansi' => $kwitansiUrl,
                'no_whatsapp' => $pelanggan->no_whatsapp ?? '08xxxxxxxxxx',
                'catatan' => $item->catatan ?? '-',
            ];
        });

    // Ambil list unik untuk filter dropdown
    $kabupatenList = $pelanggan->pluck('kabupaten')->unique();
    $kecamatanList = $pelanggan->pluck('kecamatan')->unique();

    // Statistik
    $totalCustomer = $pelanggan->count();
    $lunas = $tagihans->count(); // Jumlah tagihan yang sudah lunas
    $belumLunas = Tagihan::where('status_pembayaran', '!=', 'lunas')->count(); // Hitung tagihan belum lunas
    $totalPaket = $paket->count();

    return view('content.apps.Tagihan.tagihan-lunas', compact(
        'tagihans',
        'pelanggan',
        'paket',
        'totalCustomer',
        'lunas',
        'belumLunas',
        'totalPaket',
        'kabupatenList',
        'kecamatanList'
    ));
}


    /**
     * Update data tagihan
     */
    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_berakhir' => 'nullable|date',
            'catatan' => 'nullable|string',
            'paket_id' => 'required|exists:pakets,id',
            'bukti_pembayaran' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'kwitansi' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        $tagihan = Tagihan::findOrFail($id);
        $paket = Paket::findOrFail($request->paket_id);

        // Parse tanggal
        $tanggalMulai = \Carbon\Carbon::parse($request->tanggal_mulai);
        $tanggalBerakhir = $request->tanggal_berakhir
            ? \Carbon\Carbon::parse($request->tanggal_berakhir)
            : $tanggalMulai->copy()->addDays($paket->masa_pembayaran);

        // Handle bukti_pembayaran
        if ($request->hasFile('bukti_pembayaran')) {
            // Hapus file lama jika ada
            if ($tagihan->bukti_pembayaran && Storage::disk('public')->exists($tagihan->bukti_pembayaran)) {
                Storage::disk('public')->delete($tagihan->bukti_pembayaran);
            }

            // Simpan file baru
            $tagihan->bukti_pembayaran = $request->file('bukti_pembayaran')
                ->store('bukti_pembayaran', 'public');
        }

        // Handle kwitansi jika ada
        if ($request->hasFile('kwitansi')) {
            if ($tagihan->kwitansi && Storage::disk('public')->exists($tagihan->kwitansi)) {
                Storage::disk('public')->delete($tagihan->kwitansi);
            }

            $tagihan->kwitansi = $request->file('kwitansi')
                ->store('kwitansi', 'public');
        }

        // Update field lainnya
        $tagihan->update([
            'paket_id' => $request->paket_id,
            'tanggal_mulai' => $tanggalMulai->format('Y-m-d'),
            'tanggal_berakhir' => $tanggalBerakhir->format('Y-m-d'),
            'catatan' => $request->catatan,
        ]);

        return redirect()->back()->with('success', 'Tagihan berhasil diperbarui!');
    }


public function store(Request $request)
{
    $request->validate([
        'pelanggan_id' => 'required|exists:pelanggans,id',
        'paket_id' => 'required|exists:pakets,id',
        'tanggal_mulai' => 'required|date',
        'tanggal_berakhir' => 'nullable|date',
        'catatan' => 'nullable|string',
    ]);

    $paket = Paket::findOrFail($request->paket_id);
    $tanggalMulai = \Carbon\Carbon::parse($request->tanggal_mulai);
    $tanggalBerakhir = $request->tanggal_berakhir
        ? \Carbon\Carbon::parse($request->tanggal_berakhir)
        : $tanggalMulai->copy()->addDays($paket->masa_pembayaran);

    $tagihan = Tagihan::create([
        'pelanggan_id' => $request->pelanggan_id,
        'paket_id' => $request->paket_id,
        'harga' => $paket->harga,
        'tanggal_mulai' => $tanggalMulai->format('Y-m-d'),
        'tanggal_berakhir' => $tanggalBerakhir->format('Y-m-d'),
        'status_pembayaran' => 'belum bayar',
        'catatan' => $request->catatan,
    ]);

    $pelanggan = Pelanggan::find($request->pelanggan_id);

    // Kirim push notification jika SID tersedia
    if ($pelanggan && $pelanggan->webpushr_sid) {
        $ch = curl_init('https://api.webpushr.com/v1/notification/send/sid');

        $payload = [
    'title' => 'Pemberitahuan untuk Anda',
    'message' => "Halo {$pelanggan->nama}, kami baru saja menerbitkan tagihan untuk Anda. Silakan cek detailnya.",
    'target_url' => url('https://layanan.jernih.net.id/dashboard/customer/tagihan'),
    'sid' => $pelanggan->webpushr_sid,
];

        $headers = [
            'Content-Type: application/json',
            'webpushrKey: ' . config('services.webpushr.key'),
            'webpushrAuthToken: ' . config('services.webpushr.token'),
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);



        curl_close($ch);
    }

    return redirect()->back()->with('success', 'Tagihan berhasil ditambahkan dan notifikasi terkirim!');
}



    private function sendOneSignalNotification($playerId, $title, $message)
    {
        $content = [
            'en' => $message,
        ];

        $fields = [
            'app_id' => config('services.onesignal.app_id'),
            'include_player_ids' => [$playerId],
            'headings' => ['en' => $title],
            'contents' => $content,
        ];

        $fields = json_encode($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://onesignal.com/api/v1/notifications');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Basic ' . config('services.onesignal.rest_api_key'),
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    /**
     * Generate kode otomatis per kategori
     */
    private function getKode($kategori)
    {
        return match (strtolower($kategori)) {
            'internet' => '01',
            'penjualan' => '02',
            'piutang' => '03',
            default => 'O4', // DLL atau kategori custom
        };

    }

    // ? Update tagihan
    public function updateStatus($id)
    {
        $tagihan = \App\Models\Tagihan::with('pelanggan', 'paket')->find($id);

        if (! $tagihan) {
            return response()->json([
                'success' => false,
                'message' => 'Tagihan tidak ditemukan.',
            ], 404);
        }

        // Update status tagihan
        $tagihan->status_pembayaran = 'lunas';
        $tagihan->tanggal_pembayaran = now();
        $tagihan->save();

        // Buat data Income baru
        Income::create([
            'kode' => $this->getCode(), // atau gunakan helper getKode() jika mau auto-generate
            'kategori' => 'Tagihan',
            'jumlah' => $tagihan->jumlah_tagihan ?? $tagihan->paket->harga,
            'keterangan' => 'Pembayaran paket '.$tagihan->paket->nama_paket.' dari '.$tagihan->pelanggan->nama_lengkap,
            'tanggal_masuk' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status pembayaran berhasil diperbarui menjadi lunas dan income tercatat.',
        ]);
    }

    // ? Hapus tagihan
    public function destroy($id)
    {
        $tagihan = Tagihan::findOrFail($id);
        $tagihan->delete();

        return redirect()->back()->with('success', '??? Tagihan berhasil dihapus!');
    }

 public function massStore(Request $request)
{
    $request->validate([
        'tanggal_mulai' => 'required|date',
        'tanggal_berakhir' => 'required|date|after_or_equal:tanggal_mulai',
    ]);

    // Ambil MAX 100 pelanggan yang BELUM PUNYA TAGIHAN BELUM BAYAR
    $pelanggan = Pelanggan::with('paket')
        ->where('status', 'approve')
        ->whereNotIn('id', function ($query) {
            $query->select('pelanggan_id')
                  ->from('tagihans')
                  ->where('status_pembayaran', 'belum bayar');
        })
        ->limit(100)
        ->get();

    if ($pelanggan->isEmpty()) {
        return back()->with('error', 'Tidak ada pelanggan yang bisa dibuatkan tagihan.');
    }

    DB::beginTransaction();
    try {
        foreach ($pelanggan as $p) {
            Tagihan::create([
                'pelanggan_id' => $p->id,
                'paket_id' => $p->paket_id,
                'harga' => $p->paket->harga,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_berakhir' => $request->tanggal_berakhir,
                'status_pembayaran' => 'belum bayar',
            ]);
        }

        DB::commit();
        return back()->with('success', 'Berhasil membuat tagihan untuk 100 pelanggan berikutnya.');
    } catch (\Throwable $e) {
        DB::rollBack();
        return back()->with('error', $e->getMessage());
    }
}




public function outstanding(Request $request)
{
    // ? Base query dengan eager loading, default hanya yang belum bayar
    $query = Tagihan::with(['pelanggan', 'paket']);

    // ? Filter berdasarkan status - HANYA BELUM BAYAR (tidak bisa diubah ke lunas)
    if ($request->filled('status_filter') && $request->status_filter !== 'semua' && $request->status_filter !== 'lunas') {
        // Jika ada filter status selain 'semua' dan 'lunas', gunakan filter tersebut
        $query->where('status_pembayaran', $request->status_filter);
    } else {
        // Default: hanya ambil yang belum bayar
        $query->where('status_pembayaran', 'belum bayar');
    }

    // ? Filter berdasarkan bulan/tahun (opsional)
    if ($request->filled('bulan')) {
        $query->whereMonth('tanggal_mulai', $request->bulan);
    }

    if ($request->filled('tahun')) {
        $query->whereYear('tanggal_mulai', $request->tahun);
    }

    // ? Search functionality
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->whereHas('pelanggan', function($subQ) use ($search) {
                $subQ->where('nama_lengkap', 'LIKE', "%{$search}%")
                     ->orWhere('nomer_id', 'LIKE', "%{$search}%")
                     ->orWhere('no_whatsapp', 'LIKE', "%{$search}%");
            })
            ->orWhereHas('paket', function($subQ) use ($search) {
                $subQ->where('nama_paket', 'LIKE', "%{$search}%");
            });
        });
    }

    // ? Sorting berdasarkan tanggal terbaru
    $sortBy = $request->input('sort_by', 'created_at');
    $sortOrder = $request->input('sort_order', 'desc');
    $query->orderBy($sortBy, $sortOrder);

    // ? Pagination
    $perPage = $request->input('per_page', 20);
    $tagihans = $query->paginate($perPage)->withQueryString();

    // ? Map data untuk view
    $tagihans->getCollection()->transform(function ($item) {
        $pelanggan = $item->pelanggan;
        $paket = $item->paket;

        return (object) [
            'id' => $item->id,
            'nomer_id' => $pelanggan->nomer_id ?? '-',
            'nama_lengkap' => $pelanggan->nama_lengkap ?? '-',
            'alamat_jalan' => $pelanggan->alamat_jalan ?? '-',
            'rt' => $pelanggan->rt ?? '-',
            'rw' => $pelanggan->rw ?? '-',
            'desa' => $pelanggan->desa ?? '-',
            'kecamatan' => $pelanggan->kecamatan ?? '-',
            'kabupaten' => $pelanggan->kabupaten ?? '-',
            'provinsi' => $pelanggan->provinsi ?? '-',
            'kode_pos' => $pelanggan->kode_pos ?? '-',
            'paket' => [
                'id' => $paket->id ?? null,
                'nama_paket' => $paket->nama_paket ?? '-',
                'harga' => $paket->harga ?? 0,
                'kecepatan' => $paket->kecepatan ?? 0,
                'masa_pembayaran' => $paket->masa_pembayaran ?? 0,
                'durasi' => $paket->durasi ?? 0,
            ],
            'tanggal_mulai' => $item->tanggal_mulai ?? null,
            'tanggal_berakhir' => $item->tanggal_berakhir ?? null,
            'status_pembayaran' => $item->status_pembayaran ?? 'belum bayar',
            'tanggal_pembayaran' => $item->tanggal_pembayaran ?? '-',
            'bukti_pembayaran' => $item->bukti_pembayaran ?? '-',
            'kwitansi' => $item->kwitansi ?? null,
            'no_whatsapp' => $pelanggan->no_whatsapp ?? '08xxxxxxxxxx',
            'catatan' => $item->catatan ?? '-',
        ];
    });

    // ? Ambil pelanggan & paket untuk dropdown (jika ada modal)
    $pelanggan = Pelanggan::where('status', 'approve')->get();
    $paket = Paket::all();

    // ? Statistik Outstanding
    try {
        $totalTagihan = Tagihan::count();
        $totalBelumBayar = Tagihan::where('status_pembayaran', 'belum bayar')->count();
        $totalProses = Tagihan::where('status_pembayaran', 'proses_verifikasi')->count();
        $totalLunas = Tagihan::where('status_pembayaran', 'lunas')->count();

        // Total tagihan yang overdue (lewat tanggal jatuh tempo)
        $totalOverdue = Tagihan::where('status_pembayaran', '!=', 'lunas')
            ->where('tanggal_berakhir', '<', now())
            ->count();

        // Total nilai outstanding (belum dibayar)
        $nilaiOutstanding = Tagihan::where('status_pembayaran', 'belum bayar')
            ->join('pakets', 'tagihans.paket_id', '=', 'pakets.id')
            ->sum('pakets.harga');

        $statistics = [
            'total' => $totalTagihan,
            'belum_bayar' => $totalBelumBayar,
            'proses' => $totalProses,
            'lunas' => $totalLunas,
            'overdue' => $totalOverdue,
            'nilai_outstanding' => $nilaiOutstanding,
        ];
    } catch (\Exception $e) {
        $statistics = [
            'total' => 0,
            'belum_bayar' => 0,
            'proses' => 0,
            'lunas' => 0,
            'overdue' => 0,
            'nilai_outstanding' => 0,
        ];
    }

    // ? Filter dropdown lists
    $kabupatenList = Pelanggan::pluck('kabupaten')->unique()->filter();
    $kecamatanList = Pelanggan::pluck('kecamatan')->unique()->filter();

    // ? Bulan untuk filter
    $bulanList = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
        4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September',
        10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];

    // ? Tahun untuk filter (5 tahun terakhir)
    $tahunList = range(date('Y'), date('Y') - 4);

    return view('content.apps.Tagihan.outstanding', compact(
        'tagihans',
        'pelanggan',
        'paket',
        'statistics',
        'kabupatenList',
        'kecamatanList',
        'bulanList',
        'tahunList'
    ));
}





}
