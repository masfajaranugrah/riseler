<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Paket;
use App\Models\Pelanggan;
use App\Models\Tagihan;
use App\Models\Rekening;
use Carbon\Carbon;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BayarExport;
use App\Exports\TagihanExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class TagihanController extends Controller
{
    private const PPN_RATE = 0.11;

    private function eligibleBroadcastCustomersQuery(int $month, int $year)
    {
        return Pelanggan::query()
            ->where('status', 'approve')
            ->whereNotNull('paket_id')
            ->whereDoesntHave('tagihans', function ($q) use ($month, $year) {
                $q->whereMonth('tanggal_mulai', $month)
                    ->whereYear('tanggal_mulai', $year);
            });
    }

 /**
     * Update paket tagihan (untuk mengubah nominal jika tidak sesuai)
     * dan tanggal mulai/berakhir
     */
    public function updatePaket(Request $request, $id)
    {
        $request->validate([
            'paket_id' => 'required|exists:pakets,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_berakhir' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        DB::beginTransaction();
        try {
            $tagihan = Tagihan::with('pelanggan', 'paket')->findOrFail($id);
            $paket = Paket::findOrFail($request->paket_id);

            // Store old values for logging
            $oldPaketId = $tagihan->paket_id;
            $oldTanggalMulai = $tagihan->tanggal_mulai;
            $oldTanggalBerakhir = $tagihan->tanggal_berakhir;

            // Update paket_id, harga, dan tanggal
            $tagihan->paket_id = $paket->id;
            $tagihan->harga = $paket->harga;
            $tagihan->tanggal_mulai = $request->tanggal_mulai;
            $tagihan->tanggal_berakhir = $request->tanggal_berakhir;
            $tagihan->save();

            // Log perubahan
            Log::info('Tagihan updated', [
                'tagihan_id' => $id,
                'old_paket_id' => $oldPaketId,
                'new_paket_id' => $paket->id,
                'new_harga' => $paket->harga,
                'old_tanggal_mulai' => $oldTanggalMulai,
                'new_tanggal_mulai' => $request->tanggal_mulai,
                'old_tanggal_berakhir' => $oldTanggalBerakhir,
                'new_tanggal_berakhir' => $request->tanggal_berakhir,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tagihan berhasil diperbarui.',
                'data' => [
                    'paket_nama' => $paket->nama_paket,
                    'harga' => $paket->harga,
                    'tanggal_mulai' => $request->tanggal_mulai,
                    'tanggal_berakhir' => $request->tanggal_berakhir,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating tagihan', [
                'tagihan_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    // get data json
    public function indexGetJson()
    {
        // Ambil semua pelanggan & paket untuk dropdown modal
        $pelanggan = Pelanggan::all();
        $paket = Paket::all();

        // Ambil semua tagihan dengan status "belum bayar" beserta relasinya
        $tagihans = Tagihan::with(['pelanggan', 'paket'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                $pelanggan = $item->pelanggan;
                $paket = $item->paket;

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

                    'tanggal_mulai' => $item->tanggal_mulai,
                    'tanggal_berakhir' => $item->tanggal_berakhir,
                    'status_pembayaran' => $item->status_pembayaran,
                    'tanggal_pembayaran' => $item->tanggal_pembayaran ?? '-',
                    'bukti_pembayaran' => $item->bukti_pembayaran ?? '-',
                    'no_whatsapp' => $pelanggan->no_whatsapp ?? '08xxxxxxxxxx',
                    'catatan' => $item->catatan ?? '-',
                ];
            });

        // Ambil list unik untuk dropdown
        $kabupatenList = $pelanggan->pluck('kabupaten')->unique()->values();
        $kecamatanList = $pelanggan->pluck('kecamatan')->unique()->values();

        // Statistik
        $totalCustomer = $pelanggan->count();
        $lunas = 0;
        $belumLunas = $tagihans->count();
        $totalPaket = $paket->count();

        return response()->json([
            'status' => true,
            'message' => 'Data tagihan berhasil diambil.',
            'data' => [
                'tagihans' => $tagihans,
                'pelanggan' => $pelanggan,
                'paket' => $paket,
                'statistics' => [
                    'total_customer' => $totalCustomer,
                    'lunas' => $lunas,
                    'belum_lunas' => $belumLunas,
                    'total_paket' => $totalPaket,
                ],
                'filters' => [
                    'kabupaten' => $kabupatenList,
                    'kecamatan' => $kecamatanList,
                ],
            ],
        ]);
    }

    public function getByIdJson($id)
    {
        // Ambil data tagihan berdasarkan ID + relasi pelanggan & paket
        $item = Tagihan::with(['pelanggan', 'paket'])->find($id);

        if (!$item) {
            return response()->json([
                'status' => false,
                'message' => 'Tagihan tidak ditemukan.',
                'data' => null,
            ], 404);
        }

        $pelanggan = $item->pelanggan;
        $paket = $item->paket;

        // Bentuk JSON detail (sama dengan indexGetJson)
        $tagihanDetail = [
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

            'tanggal_mulai' => $item->tanggal_mulai,
            'tanggal_berakhir' => $item->tanggal_berakhir,
            'status_pembayaran' => $item->status_pembayaran,
            'tanggal_pembayaran' => $item->tanggal_pembayaran ?? '-',
            'bukti_pembayaran' => $item->bukti_pembayaran ?? '-',
            'no_whatsapp' => $pelanggan->no_whatsapp ?? '08xxxxxxxxxx',
            'catatan' => $item->catatan ?? '-',
        ];

        return response()->json([
            'status' => true,
            'message' => 'Detail tagihan berhasil diambil.',
            'data' => $tagihanDetail,
        ]);
    }




    public function konfirmasiBayar(Request $request, $id)
    {
        $tagihan = Tagihan::with('pelanggan', 'paket')->findOrFail($id);

        DB::beginTransaction();
        try {
            // Upload bukti pembayaran (opsional)
            if ($request->hasFile('bukti_pembayaran')) {
                $file = $request->file('bukti_pembayaran');
                $path = $file->store('bukti_pembayaran', 'public');
                $tagihan->bukti_pembayaran = $path;
            }

            // Simpan tipe pembayaran jika dikirim dari admin
            if ($request->filled('type_pembayaran')) {
                $typePembayaran = $request->input('type_pembayaran');
                // Jika "cash" atau kosong → null (Cash/Tunai), selain itu simpan UUID rekening apa adanya
                $tagihan->type_pembayaran = ($typePembayaran === 'cash' || empty($typePembayaran))
                    ? null
                    : $typePembayaran;
            }

            $nominalTagihan = (float) ($tagihan->harga ?? $tagihan->paket->harga ?? 0);
            $kenaPpn = (bool) ($tagihan->pelanggan->kena_ppn ?? true);
            $ppnNominal = $kenaPpn ? (int) round($nominalTagihan * self::PPN_RATE) : 0;
            $nominalSetelahPotonganPpn = max(0, (int) round($nominalTagihan - $ppnNominal));

            // Update status tagihan menjadi lunas
            $tagihan->status_pembayaran = 'lunas';
            $tagihan->tanggal_pembayaran = now();
            // Nilai yang masuk ke administrasi "Masuk" adalah nilai setelah potongan PPN
            $tagihan->harga = $nominalSetelahPotonganPpn;

            // Generate PDF kwitansi
            $pdf = Pdf::loadView('content.apps.pdf.kwitansi', ['tagihan' => $tagihan]);
            $filename = 'kwitansi-' . $tagihan->id . '.pdf';
            $pdfPath = 'kwitansi/' . $filename;
            Storage::disk('public')->put($pdfPath, $pdf->output());

            // Simpan path PDF ke field kwitansi
            $tagihan->kwitansi = $pdfPath;
            $tagihan->save();

            // Buat link publik PDF
            $pdfUrl = asset('storage/' . $pdfPath);

            // Buat record Income
            Income::create([
                'kode' => $this->getKode('penjualan'),
                'kategori' => 'penjualan',
                'jumlah' => $nominalSetelahPotonganPpn,
                'keterangan' => 'Pembayaran paket ' . $tagihan->paket->nama_paket . ' dari ' . $tagihan->pelanggan->nama_lengkap,
                'pelanggan_id' => $tagihan->pelanggan_id,
                'tanggal_masuk' => now(),
            ]);

            if ($kenaPpn && $ppnNominal > 0) {
                Income::create([
                    'kode' => $this->getKode('potongan ppn'),
                    'kategori' => 'Potongan PPN',
                    'jumlah' => $ppnNominal,
                    'keterangan' => 'Potongan PPN ' . (self::PPN_RATE * 100) . '% dari pembayaran paket ' . $tagihan->paket->nama_paket . ' - ' . $tagihan->pelanggan->nama_lengkap,
                    'pelanggan_id' => $tagihan->pelanggan_id,
                    'tanggal_masuk' => now(),
                ]);
            }

            // Notifikasi WebPush dihapus sesuai permintaan

            // ===== Mikrotik Restore Logic (optional) =====
            if (class_exists(\App\Models\Router::class) && class_exists(\App\Services\MikrotikService::class)) {
                try {
                    $routers = \App\Models\Router::all();
                    $service = new \App\Services\MikrotikService();
                    foreach ($routers as $router) {
                        try {
                            // Coba connect ke setiap router (karena kita belum tau user ada di mana)
                            if ($service->connect($router)) {
                                $username = $tagihan->pelanggan->nomer_id; // Asumsi nomer_id = PPPoE user
                                $profile = $tagihan->paket->mikrotik_profile;
                                
                                if ($username && $profile) {
                                    // Restore profile asli
                                    $service->restoreCustomer($username, $profile);
                                }
                            }
                        } catch (\Exception $e) {
                            // Log error tapi jangan gagalkan transaksi pembayaran
                            Log::error('Mikrotik Restore Failed for Router ' . $router->name . ': ' . $e->getMessage());
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('Mikrotik Service Error: ' . $e->getMessage());
                }
            } else {
                Log::warning('Mikrotik Restore skipped: Router model or service missing');
            }
            // =============================================

            DB::commit();

            return response()->json([
                'success' => true,
                'pdfUrl' => $pdfUrl,
                'message' => 'Pembayaran berhasil dikonfirmasi dan notifikasi terkirim!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }


    }




    /**
     * Contoh fungsi helper untuk kirim WA (dummy)
     */
    private function sendWA($nomor, $pesan)
    {
        // TODO: implementasi request ke API WhatsApp
        // return true jika berhasil, false jika gagal
        return true;
    }


    public function index(Request $request)
    {
        // Ambil semua pelanggan & paket untuk dropdown modal
        // ? MENGHAPUS QUERY $pelanggan YANG MENGAMBIL SEMUA DATA
        // Karena di view tagihan.blade.php sudah menggunakan AJAX select2, query ini hanya membuang memori dan waktu.

        $paket = Paket::all();

        // ? BUILD QUERY OPTIMIZATION - MENGGUNAKAN JOIN (LEBIH CEPAT DARI WHEREHAS)
        $query = Tagihan::with(['pelanggan', 'paket'])
            ->select('tagihans.*') // Wajib select tagihans.* agar id tagihan tidak tertimpa id pelanggan
            ->join('pelanggans', 'tagihans.pelanggan_id', '=', 'pelanggans.id')
            ->where('tagihans.status_pembayaran', 'belum bayar');

        // ? SEARCH FILTER - OPTIMALKAN DENGAN PELANGGANS. PREFIX
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

        // ? FILTER BULAN & TAHUN
        if ($request->filled('periode')) {
            $periode = $request->periode; // format: 2026-01
            $parts = explode('-', $periode);
            if (count($parts) === 2) {
                $tahun = (int) $parts[0];
                $bulan = (int) $parts[1];
                $query->whereYear('tagihans.tanggal_mulai', $tahun)
                      ->whereMonth('tagihans.tanggal_mulai', $bulan);
            }
        }

        // ? PAGINATION
        $tagihans = $query
            ->orderBy('tagihans.created_at', 'desc')
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

        // ? List kabupaten & kecamatan juga dihapus (tidak perlu lagi)
        // $kabupatenList = ...
        // $kecamatanList = ...

        // Statistik
        $totalCustomer = Pelanggan::where('status', 'approve')->count();
        // Gunakan total tagihan lunas (bukan distinct pelanggan) agar angka konsisten dengan daftar
        $customerLunas = Tagihan::where('status_pembayaran', 'lunas')->count();
        $lunas = $customerLunas; // Jumlah tagihan lunas
        $belumLunas = Tagihan::where('status_pembayaran', 'belum bayar')->count();
        $totalPaket = $paket->count();

        // Rekening list untuk dropdown verifikasi
        $rekeningList = \App\Models\Rekening::select('id', 'nama_bank')->get();

        // ? RETURN VIEW HTML (tanpa kabupatenList & kecamatanList)
        return view('content.apps.Tagihan.tagihan', [
            'tagihans'      => $tagihans,
            'pelanggan'     => [], // Kirim array kosong karena tidak dipakai lagi di view
            'paket'         => $paket,
            'totalCustomer' => $totalCustomer,
            'customerLunas' => $customerLunas,
            'lunas'         => $lunas,
            'belumLunas'    => $belumLunas,
            'totalPaket'    => $totalPaket,
            'rekeningList'  => $rekeningList,
        ]);
    }



    public function proses(Request $request)
    {
        // Ambil semua pelanggan & paket untuk dropdown modal
        $pelanggan = Pelanggan::all();
        $paket = Paket::all();

        // Query builder dengan search
        $query = Tagihan::with(['pelanggan', 'paket'])
            ->where('status_pembayaran', 'proses_verifikasi');

        // Tambahkan filter search jika ada parameter
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('pelanggan', function ($subQ) use ($search) {
                    $subQ->where('nama_lengkap', 'LIKE', "%{$search}%")
                        ->orWhere('nomer_id', 'LIKE', "%{$search}%")
                        ->orWhere('no_whatsapp', 'LIKE', "%{$search}%")
                        ->orWhere('alamat_jalan', 'LIKE', "%{$search}%")
                        ->orWhere('desa', 'LIKE', "%{$search}%")
                        ->orWhere('kecamatan', 'LIKE', "%{$search}%")
                        ->orWhere('kabupaten', 'LIKE', "%{$search}%");
                })
                    ->orWhereHas('paket', function ($subQ) use ($search) {
                        $subQ->where('nama_paket', 'LIKE', "%{$search}%");
                    });
            });
        }

        // Pagination dengan withQueryString untuk mempertahankan parameter search
        $tagihans = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

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

    /**
     * Update status tagihan dari proses_verifikasi kembali ke belum bayar
     * dan hapus bukti pembayaran yang salah
     */
    public function updateStatusToBelumBayar($id)
    {
        DB::beginTransaction();
        try {
            $tagihan = Tagihan::with('pelanggan', 'paket')->findOrFail($id);

            // Validasi: hanya bisa update jika statusnya proses_verifikasi
            if ($tagihan->status_pembayaran !== 'proses_verifikasi') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya tagihan dengan status "Proses Verifikasi" yang bisa diubah ke "Belum Bayar".',
                ], 400);
            }

            // Hapus bukti pembayaran jika ada
            if ($tagihan->bukti_pembayaran && Storage::disk('public')->exists($tagihan->bukti_pembayaran)) {
                Storage::disk('public')->delete($tagihan->bukti_pembayaran);
            }

            // Update status ke belum bayar dan hapus bukti pembayaran
            $tagihan->status_pembayaran = 'belum bayar';
            $tagihan->bukti_pembayaran = null;
            $tagihan->tanggal_pembayaran = null;
            $tagihan->save();

            // Notifikasi WebPush dihapus sesuai permintaan

            // ===== Mikrotik Isolate Logic (optional) =====
            if (class_exists(\App\Models\Router::class) && class_exists(\App\Services\MikrotikService::class)) {
                try {
                    $routers = \App\Models\Router::all();
                    $service = new \App\Services\MikrotikService();
                    foreach ($routers as $router) {
                        try {
                            if ($service->connect($router)) {
                                $username = $tagihan->pelanggan->nomer_id;
                                
                                if ($username) {
                                    // Set profile ke 'isolir' (pastikan profile ini ada di Mikrotik)
                                    $service->isolateCustomer($username, 'isolir');
                                }
                            }
                        } catch (\Exception $e) {
                            Log::error('Mikrotik Isolate Failed for Router ' . $router->name . ': ' . $e->getMessage());
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('Mikrotik Service Error: ' . $e->getMessage());
                }
            } else {
                Log::warning('Mikrotik Isolate skipped: Router model or service missing');
            }
            // =============================================

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status tagihan berhasil diubah ke "Belum Bayar" dan bukti pembayaran telah dihapus. Notifikasi telah dikirim ke pelanggan.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating tagihan status to belum bayar', [
                'tagihan_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }



public function lunas(Request $request)
    {
        // Eager load hanya kolom yang diperlukan
        $query = Tagihan::with([
            'pelanggan:id,nama_lengkap,nomer_id,no_whatsapp,alamat_jalan,rt,rw,desa,kecamatan,kabupaten,provinsi,kode_pos', 
            'paket:id,nama_paket,harga,kecepatan,masa_pembayaran', 
            'rekening:id,nama_bank'
        ])->where('status_pembayaran', 'lunas');

        // Filter search
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->whereHas('pelanggan', function($subQ) use ($search) {
                    $subQ->where('nama_lengkap', 'LIKE', "%{$search}%")
                         ->orWhere('nomer_id', 'LIKE', "%{$search}%")
                         ->orWhere('no_whatsapp', 'LIKE', "%{$search}%");
                });
            });
        }

        // Filter periode (bulan dan tahun)
        if ($bulan = $request->input('bulan')) {
            $query->whereMonth('tanggal_mulai', $bulan);
        }
        
        if ($tahun = $request->input('tahun')) {
            $query->whereYear('tanggal_mulai', $tahun);
        }

        // Filter Bank
        if ($bankId = $request->input('bank')) {
            $query->where('type_pembayaran', $bankId);
        }

        // PAGINATION - langsung return model tanpa through() untuk kecepatan
        $tagihans = $query->orderBy('created_at', 'desc')
            ->paginate(40)
            ->withQueryString();

        // Bank totals - query sederhana dengan filter yang sama
        $bankTotalsQuery = Tagihan::leftJoin('rekenings', 'rekenings.id', '=', 'tagihans.type_pembayaran')
            ->leftJoin('pakets', 'pakets.id', '=', 'tagihans.paket_id')
            ->where('tagihans.status_pembayaran', 'lunas');
        
        // Apply same filters untuk bankTotals
        if ($bulan = $request->input('bulan')) {
            $bankTotalsQuery->whereMonth('tagihans.tanggal_mulai', $bulan);
        }
        if ($tahun = $request->input('tahun')) {
            $bankTotalsQuery->whereYear('tagihans.tanggal_mulai', $tahun);
        }
        if ($bankId = $request->input('bank')) {
            $bankTotalsQuery->where('tagihans.type_pembayaran', $bankId);
        }
        
        $bankTotals = $bankTotalsQuery
            ->selectRaw('COALESCE(rekenings.nama_bank, tagihans.type_pembayaran, "Lainnya") as nama_bank, SUM(COALESCE(tagihans.harga, pakets.harga, 0)) as total')
            ->groupByRaw('COALESCE(rekenings.nama_bank, tagihans.type_pembayaran, "Lainnya")')
            ->orderByDesc('total')
            ->get();

        // Ambil list bank untuk filter dropdown - query ringan
        $rekeningList = Rekening::select('id', 'nama_bank')->get();

        return view('content.apps.Tagihan.tagihan-lunas', compact(
            'tagihans',
            'rekeningList',
            'bankTotals'
        ));
    }



    public function searchPelanggan(Request $request)
    {
        $term = $request->q;

        $query = Pelanggan::with('paket')
            ->where('status', 'approve');

        if ($term) {
            $query->where(function ($q) use ($term) {
                $q->where('nama_lengkap', 'LIKE', "%{$term}%")
                    ->orWhere('nomer_id', 'LIKE', "%{$term}%")
                    ->orWhere('no_whatsapp', 'LIKE', "%{$term}%");
            });
        }

        $pelanggans = $query->paginate(20);

        $results = [];
        foreach ($pelanggans as $p) {
            $results[] = [
                'id' => $p->id,
                'text' => $p->nomer_id . ' - ' . $p->nama_lengkap,
                'nama' => $p->nama_lengkap, // Untuk data-attribute
                'nomorid' => $p->nomer_id,
                'nowhatsapp' => $p->no_whatsapp,
                'alamat_jalan' => $p->alamat_jalan,
                'rt' => $p->rt,
                'rw' => $p->rw,
                'desa' => $p->desa,
                'kecamatan' => $p->kecamatan,
                'kabupaten' => $p->kabupaten,
                'provinsi' => $p->provinsi,
                'kode_pos' => $p->kode_pos,
                'paket' => $p->paket->nama_paket ?? '-',
                'paket_id' => $p->paket_id,
                'harga' => $p->paket->harga ?? 0,
                'masa' => $p->paket->masa_pembayaran ?? 0,
                'kecepatan' => $p->paket->kecepatan ?? 0,
            ];
        }

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => $pelanggans->hasMorePages()
            ]
        ]);
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

        $message = 'Tagihan berhasil ditambahkan.';
        return redirect()->back()->with('success', $message);
    }


    private function sendOneSignalNotification($playerId, $title, $message)
    {
        $content = [
            'en' => $message,
        ];

        $fields = [
            'app_id' => env('ONESIGNAL_APP_ID'),
            'include_player_ids' => [$playerId],
            'headings' => ['en' => $title],
            'contents' => $content,
        ];

        $fields = json_encode($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://onesignal.com/api/v1/notifications');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Basic ' . env('ONESIGNAL_REST_API_KEY'),
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

        if (!$tagihan) {
            return response()->json([
                'success' => false,
                'message' => 'Tagihan tidak ditemukan.',
            ], 404);
        }

        $nominalTagihan = (float) ($tagihan->harga ?? $tagihan->paket->harga ?? 0);
        $kenaPpn = (bool) ($tagihan->pelanggan->kena_ppn ?? true);
        $ppnNominal = $kenaPpn ? (int) round($nominalTagihan * self::PPN_RATE) : 0;
        $nominalSetelahPotonganPpn = max(0, (int) round($nominalTagihan - $ppnNominal));

        // Update status tagihan
        $tagihan->status_pembayaran = 'lunas';
        $tagihan->tanggal_pembayaran = now();
        $tagihan->harga = $nominalSetelahPotonganPpn;
        $tagihan->save();

        // Buat data Income baru
        Income::create([
            'kode' => $this->getCode(), // atau gunakan helper getKode() jika mau auto-generate
            'kategori' => 'Tagihan',
            'jumlah' => $nominalSetelahPotonganPpn,
            'keterangan' => 'Pembayaran paket ' . $tagihan->paket->nama_paket . ' dari ' . $tagihan->pelanggan->nama_lengkap,
            'pelanggan_id' => $tagihan->pelanggan_id,
            'tanggal_masuk' => now(),
        ]);

        if ($kenaPpn && $ppnNominal > 0) {
            Income::create([
                'kode' => $this->getKode('potongan ppn'),
                'kategori' => 'Potongan PPN',
                'jumlah' => $ppnNominal,
                'keterangan' => 'Potongan PPN ' . (self::PPN_RATE * 100) . '% dari pembayaran paket ' . $tagihan->paket->nama_paket . ' - ' . $tagihan->pelanggan->nama_lengkap,
                'pelanggan_id' => $tagihan->pelanggan_id,
                'tanggal_masuk' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Status pembayaran berhasil diperbarui menjadi lunas dan income tercatat.',
        ]);
    }

    // ? Hapus tagihan
    public function destroy(Request $request, $id)
    {
        $tagihan = Tagihan::findOrFail($id);
        $tagihan->delete();

        // Jika request AJAX, return JSON (supaya bisa hapus baris tanpa reload)
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Tagihan berhasil dihapus!'
            ]);
        }

        return redirect()->back()->with('success', 'Tagihan berhasil dihapus!');
    }

    // Hapus tagihan hanya jika status lunas
  public function destroyLunas($id)
    {
        $tagihan = Tagihan::findOrFail($id);
        if ($tagihan->status_pembayaran !== 'lunas') {
            return redirect()->back()->with('error', 'Tagihan yang dihapus harus berstatus lunas!');
        }

        try {
            // Hapus file bukti pembayaran jika ada
            if ($tagihan->bukti_pembayaran) {
                // Bersihkan path dari prefix 'storage/' jika ada
                $buktiPath = $tagihan->bukti_pembayaran;
                if (str_starts_with($buktiPath, 'storage/')) {
                    $buktiPath = substr($buktiPath, 8); // Remove 'storage/' prefix
                }
                
                if (Storage::disk('public')->exists($buktiPath)) {
                    Storage::disk('public')->delete($buktiPath);
                }
            }

            // Hapus file kwitansi jika ada
            if ($tagihan->kwitansi) {
                // Bersihkan path dari prefix 'storage/' jika ada
                $kwitansiPath = $tagihan->kwitansi;
                if (str_starts_with($kwitansiPath, 'storage/')) {
                    $kwitansiPath = substr($kwitansiPath, 8); // Remove 'storage/' prefix
                }
                
                if (Storage::disk('public')->exists($kwitansiPath)) {
                    Storage::disk('public')->delete($kwitansiPath);
                }
            }
        } catch (\Exception $e) {
            // Log error tapi tetap lanjut hapus tagihan
            Log::warning('Error menghapus file tagihan lunas', [
                'tagihan_id' => $id,
                'error' => $e->getMessage(),
            ]);
        }

        $tagihan->delete();

        return redirect()->route('tagihan.lunas')->with('success', 'Tagihan lunas berhasil dihapus!');
    }

    /**
     * Get count of eligible customers for broadcast tagihan
     * (semua pelanggan yang belum punya tagihan di bulan ini)
     */
    public function getBroadcastCount()
    {
        try {
            $currentMonth = now()->month;
            $currentYear = now()->year;

            $count = $this->eligibleBroadcastCustomersQuery($currentMonth, $currentYear)->count();

            return response()->json(['count' => $count]);
        } catch (\Exception $e) {
            Log::error('Error getting broadcast count', ['error' => $e->getMessage()]);
            return response()->json(['count' => 0, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get IDs of eligible customers for broadcast tagihan
     * (semua pelanggan yang belum punya tagihan di bulan ini)
     */
    public function getBroadcastIds()
    {
        try {
            $currentMonth = now()->month;
            $currentYear = now()->year;

            $ids = $this->eligibleBroadcastCustomersQuery($currentMonth, $currentYear)
                ->pluck('id')
                ->toArray();

            return response()->json(['ids' => $ids]);
        } catch (\Exception $e) {
            Log::error('Error getting broadcast ids', ['error' => $e->getMessage()]);
            return response()->json(['ids' => [], 'error' => $e->getMessage()], 500);
        }
    }

    public function massStore(Request $request)
    {
        // Extend execution time for batch processing
        set_time_limit(300); // 5 minutes for large batches

        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_berakhir' => 'required|date|after_or_equal:tanggal_mulai',
            'pelanggan_ids' => 'required|array|min:1',
            'pelanggan_ids.*' => 'exists:pelanggans,id',
        ]);

        $pelangganIds = $request->pelanggan_ids;

        // Ambil pelanggan yang dipilih, filter yang belum ada tagihan pada periode tanggal_mulai
        $periodDate = Carbon::parse($request->tanggal_mulai);
        $periodMonth = $periodDate->month;
        $periodYear = $periodDate->year;

        $pelanggan = $this->eligibleBroadcastCustomersQuery($periodMonth, $periodYear)
            ->whereIn('id', $pelangganIds)
            ->with('paket')
            ->get();

        if ($pelanggan->isEmpty()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'processed' => 0,
                    'failed' => count($pelangganIds),
                    'message' => 'Tidak ada pelanggan yang bisa dibuatkan tagihan.',
                ]);
            }
            return back()->with('error', 'Tidak ada pelanggan yang bisa dibuatkan tagihan. Mungkin semua sudah memiliki tagihan belum bayar.');
        }

        $successCount = 0;
        $failedCount = 0;

        DB::beginTransaction();
        try {
            foreach ($pelanggan as $p) {
                if (!$p->paket_id || !$p->paket) {
                    $failedCount++;
                    continue;
                }

                $newTagihan = Tagihan::create([
                    'pelanggan_id' => $p->id,
                    'paket_id' => $p->paket_id,
                    'harga' => $p->paket->harga,
                    'tanggal_mulai' => $request->tanggal_mulai,
                    'tanggal_berakhir' => $request->tanggal_berakhir,
                    'status_pembayaran' => 'belum bayar',
                ]);

                $successCount++;
            }

            DB::commit();

            $message = "Berhasil membuat tagihan untuk {$successCount} pelanggan.";
            if ($failedCount > 0) {
                $message .= " {$failedCount} pelanggan gagal (tidak memiliki paket atau sudah memiliki tagihan).";
            }

            // Return JSON for AJAX requests, redirect for regular form submissions
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'processed' => $successCount,
                    'failed' => $failedCount,
                    'message' => $message,
                ]);
            }

            return back()->with('success', $message);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error mass store tagihan', [
                'error' => $e->getMessage(),
                'pelanggan_ids' => $pelangganIds,
            ]);

            // Return JSON for AJAX requests, redirect for regular form submissions
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'processed' => 0,
                    'failed' => count($pelangganIds),
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }





    public function export(Request $request)
    {
        $search = $request->input('search');
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $bank = $request->input('bank');

        $filename = 'Tagihan_Lunas_' .
            ($bulan ? 'B' . $bulan . '_' : '') .
            ($tahun ? 'Y' . $tahun . '_' : '') .
            now()->format('Y-m-d_His') .
            '.xlsx';

        return Excel::download(
            new BayarExport($search, 'lunas', $bulan, $tahun, $bank),
            $filename
        );
    }

    /**
     * Export tagihan yang belum lunas (status: belum bayar)
     */
    public function exportBelumLunas(Request $request)
    {
        $search = $request->input('search');
        $periode = $request->input('periode'); // format: 2026-01

        // Generate filename
        $filename = 'Tagihan_Belum_Lunas_' . ($periode ? $periode . '_' : '') . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(
            new TagihanExport('belum bayar', null, null, $search, $periode),
            $filename
        );
    }

    /**
     * Halaman Outstanding - Semua Tagihan (Tanpa Filter Status)
     * Berguna untuk melihat semua tagihan dari bulan lain
     */
    public function outstanding(Request $request)
    {
        // ? Base query dengan eager loading, default hanya yang belum lunas
        $query = Tagihan::with(['pelanggan', 'paket'])
            ->where('status_pembayaran', 'belum bayar');

        // ? Filter berdasarkan bulan/tahun (opsional)
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_mulai', $request->bulan);
        }

        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_mulai', $request->tahun);
        }

        // ? Filter berdasarkan status (opsional, jika dipilih selain 'semua', override default)
        if ($request->filled('status_filter') && $request->status_filter !== 'semua') {
            $query->where('status_pembayaran', $request->status_filter); // Filter sesuai status
        }
        // Jika status_filter == 'semua', jangan override filter default (hanya 'belum bayar')

        // ? Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('pelanggan', function ($subQ) use ($search) {
                    $subQ->where('nama_lengkap', 'LIKE', "%{$search}%")
                        ->orWhere('nomer_id', 'LIKE', "%{$search}%")
                        ->orWhere('no_whatsapp', 'LIKE', "%{$search}%");
                })
                    ->orWhereHas('paket', function ($subQ) use ($search) {
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
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
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
