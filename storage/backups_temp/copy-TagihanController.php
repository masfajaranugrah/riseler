<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Paket;
use App\Models\Pelanggan;
use App\Models\Tagihan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BayarExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


class TagihanController extends Controller
{
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

            // Update status tagihan menjadi lunas
            $tagihan->status_pembayaran = 'lunas';
            $tagihan->tanggal_pembayaran = now();

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
                'jumlah' => $tagihan->jumlah_tagihan ?? $tagihan->paket->harga,
                'keterangan' => 'Pembayaran paket ' . $tagihan->paket->nama_paket . ' dari ' . $tagihan->pelanggan->nama_lengkap,
                'tanggal_masuk' => now(),
            ]);

            // ===== Kirim push notification sebelum return =====
            $pelanggan = $tagihan->pelanggan;
            if ($pelanggan && $pelanggan->webpushr_sid) {
                $end_point = 'https://api.webpushr.com/v1/notification/send/sid';

                $http_header = [
                    'Content-Type: Application/Json',
                    'webpushrKey: 2ee12b373a17d9ba5f44683cb42d4279', // ganti dengan API key Webpushr
                    'webpushrAuthToken: 116294', // ganti dengan Auth Token Webpushr
                ];

                $req_data = [
                    'title' => 'Pembayaran Berhasil',
                    'message' => "Terima kasih, {$pelanggan->nama_lengkap}. Pembayaran Anda telah kami terima dan dikonfirmasi.",
                    'target_url' => url('https://layanan.jernih.net.id/dashboard/customer/tagihan/selesai'), // link ke halaman tagihan
                    'sid' => $pelanggan->webpushr_sid,
                ];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_HTTPHEADER, $http_header);
                curl_setopt($ch, CURLOPT_URL, $end_point);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($req_data));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);
                curl_close($ch);

                // Optional: log response untuk debug
            }
            // ===================================================

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
        $pelanggan = Pelanggan::where('status', 'approve')
            ->whereDoesntHave('tagihans', function ($q) {
                $q->where('status_pembayaran', 'belum bayar');
            })
            ->get();

        $paket = Paket::all();

        // ? BUILD QUERY - HANYA STATUS "BELUM BAYAR"
        $query = Tagihan::with(['pelanggan', 'paket'])
            ->where('status_pembayaran', 'belum bayar');

        // ? SEARCH FILTER - HANYA DI STATUS "BELUM BAYAR"
        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->whereHas('pelanggan', function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nomer_id', 'like', "%{$search}%")          // Cari di No. ID
                    ->orWhere('no_whatsapp', 'like', "%{$search}%")       // Cari di WhatsApp
                    ->orWhere('no_telp', 'like', "%{$search}%")           // Cari di Telepon
                    ->orWhere('alamat_jalan', 'like', "%{$search}%")      // Cari di Alamat
                    ->orWhere('rt', 'like', "%{$search}%")                // Cari di RT
                    ->orWhere('rw', 'like', "%{$search}%")                // Cari di RW
                    ->orWhere('desa', 'like', "%{$search}%")              // Cari di Desa
                    ->orWhere('kecamatan', 'like', "%{$search}%")         // Cari di Kecamatan
                    ->orWhere('kabupaten', 'like', "%{$search}%")         // Cari di Kabupaten
                    ->orWhere('kode_pos', 'like', "%{$search}%");         // Cari di Kode Pos
            });
        }

        // ? FILTER KABUPATEN & KECAMATAN DIHAPUS

        // ? PAGINATION
        $tagihans = $query
            ->orderBy('created_at', 'desc')
            ->paginate(40)
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

        // ? RETURN VIEW HTML (tanpa kabupatenList & kecamatanList)
        return view('content.apps.Tagihan.tagihan', [
            'tagihans' => $tagihans,
            'pelanggan' => $pelanggan,
            'paket' => $paket,
            'totalCustomer' => $totalCustomer,
            'customerLunas' => $customerLunas,
            'lunas' => $lunas,
            'belumLunas' => $belumLunas,
            'totalPaket' => $totalPaket,
            // ? kabupatenList & kecamatanList dihapus
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

            // ===== Kirim push notification sebelum return =====
            $pelanggan = $tagihan->pelanggan;
            if ($pelanggan && $pelanggan->webpushr_sid) {
                $end_point = 'https://api.webpushr.com/v1/notification/send/sid';

                $http_header = [
                    'Content-Type: Application/Json',
                    'webpushrKey: 2ee12b373a17d9ba5f44683cb42d4279', // ganti dengan API key Webpushr
                    'webpushrAuthToken: 116294', // ganti dengan Auth Token Webpushr
                ];

                $req_data = [
                    'title' => 'Status Tagihan Diperbarui',
                    'message' => "Halo {$pelanggan->nama_lengkap}, status tagihan Anda telah dikembalikan ke 'Belum Bayar'. Silakan upload bukti pembayaran yang benar.",
                    'target_url' => url('https://layanan.jernih.net.id/dashboard/customer/tagihan'), // link ke halaman tagihan
                    'sid' => $pelanggan->webpushr_sid,
                ];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_HTTPHEADER, $http_header);
                curl_setopt($ch, CURLOPT_URL, $end_point);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($req_data));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);
                curl_close($ch);

                // Optional: log response untuk debug
            }
            // ===================================================

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
        // Query builder dengan search - HANYA YANG LUNAS
        $query = Tagihan::with(['pelanggan', 'paket', 'rekening'])
            ->where('status_pembayaran', 'lunas');

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
                    })
                    ->orWhereHas('rekening', function ($subQ) use ($search) {
                        $subQ->where('nama_bank', 'LIKE', "%{$search}%");
                    });
            });
        }

        // PAGINATION 40 DATA PER PAGE dengan through()
        $tagihans = $query->orderBy('created_at', 'desc')
            ->paginate(40)
            ->withQueryString()
            ->through(function ($item) {
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
        $kabupatenList = Pelanggan::distinct()->pluck('kabupaten')->filter();
        $kecamatanList = Pelanggan::distinct()->pluck('kecamatan')->filter();

        // Statistik
        // Jumlah customer unik yang sudah lunas
        $totalCustomerLunas = Tagihan::where('status_pembayaran', 'lunas')->distinct('pelanggan_id')->count('pelanggan_id');
        $lunas = Tagihan::where('status_pembayaran', 'lunas')->count(); // Jumlah tagihan lunas
        $belumLunas = Tagihan::where('status_pembayaran', 'belum bayar')->count();
        $totalPaket = Paket::count();

        // Rekap total pembayaran per bank untuk kartu ringkasan
        $bankTotals = Tagihan::leftJoin('rekenings', 'rekenings.id', '=', 'tagihans.type_pembayaran')
            ->leftJoin('pakets', 'pakets.id', '=', 'tagihans.paket_id')
            ->where('tagihans.status_pembayaran', 'lunas')
            ->selectRaw('COALESCE(rekenings.nama_bank, tagihans.type_pembayaran, "Lainnya") as nama_bank, SUM(COALESCE(tagihans.harga, pakets.harga, 0)) as total')
            ->groupByRaw('COALESCE(rekenings.nama_bank, tagihans.type_pembayaran, "Lainnya")')
            ->orderByDesc('total')
            ->get();

        return view('content.apps.Tagihan.tagihan-lunas', compact(
            'tagihans',
            'totalCustomerLunas',
            'lunas',
            'belumLunas',
            'totalPaket',
            'kabupatenList',
            'kecamatanList',
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
                'webpushrKey: 2ee12b373a17d9ba5f44683cb42d4279',
                'webpushrAuthToken: 116294',
            ];

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);

            curl_close($ch);

            // Decode response
            $responseData = json_decode($response, true);

            // Log semua detail response
            Log::info('Webpushr Response', [
                'pelanggan_id' => $pelanggan->id,
                'pelanggan_nama' => $pelanggan->nama,
                'sid' => $pelanggan->webpushr_sid,
                'http_code' => $httpCode,
                'curl_error' => $curlError,
                'response' => $responseData,
                'payload_sent' => $payload
            ]);

            // Cek status response dan tentukan message
            if ($httpCode == 200 && isset($responseData['success']) && $responseData['success']) {
                // API sukses dipanggil
                if (isset($responseData['sent']) && $responseData['sent'] > 0) {
                    // Notifikasi berhasil terkirim
                    $message = 'Tagihan berhasil ditambahkan dan notifikasi terkirim!';
                } else {
                    // API sukses tapi notifikasi gagal/ignored
                    Log::warning('Webpushr notification tidak terkirim', [
                        'pelanggan_id' => $pelanggan->id,
                        'failed' => $responseData['failed'] ?? 0,
                        'ignored' => $responseData['ignored'] ?? 0,
                        'sent' => $responseData['sent'] ?? 0,
                        'message' => $responseData['message'] ?? 'No message'
                    ]);
                    $message = 'Tagihan berhasil ditambahkan, tapi notifikasi gagal terkirim. Periksa subscription pelanggan.';
                }
            } else {
                // API error
                Log::error('Webpushr API error', [
                    'pelanggan_id' => $pelanggan->id,
                    'http_code' => $httpCode,
                    'curl_error' => $curlError,
                    'response' => $responseData
                ]);
                $message = 'Tagihan berhasil ditambahkan, tapi notifikasi gagal terkirim (API error).';
            }
        } else {
            $message = 'Tagihan berhasil ditambahkan (tanpa notifikasi - SID tidak tersedia).';

            Log::info('Tagihan dibuat tanpa notifikasi', [
                'pelanggan_id' => $request->pelanggan_id,
                'reason' => $pelanggan ? 'SID tidak tersedia' : 'Pelanggan tidak ditemukan'
            ]);
        }

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

        // Update status tagihan
        $tagihan->status_pembayaran = 'lunas';
        $tagihan->tanggal_pembayaran = now();
        $tagihan->save();

        // Buat data Income baru
        Income::create([
            'kode' => $this->getCode(), // atau gunakan helper getKode() jika mau auto-generate
            'kategori' => 'Tagihan',
            'jumlah' => $tagihan->jumlah_tagihan ?? $tagihan->paket->harga,
            'keterangan' => 'Pembayaran paket ' . $tagihan->paket->nama_paket . ' dari ' . $tagihan->pelanggan->nama_lengkap,
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

        return redirect()->route('tagihan.get')->with('success', 'Tagihan berhasil dihapus!');
    }

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
     * (approved, has paket, no unpaid tagihan)
     */
    public function getBroadcastCount()
    {
        try {
            $count = Pelanggan::where('status', 'approve')
                ->whereNotNull('paket_id')
                ->whereDoesntHave('tagihans', function ($q) {
                    $q->where('status_pembayaran', 'belum bayar');
                })
                ->count();

            return response()->json(['count' => $count]);
        } catch (\Exception $e) {
            Log::error('Error getting broadcast count', ['error' => $e->getMessage()]);
            return response()->json(['count' => 0, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get IDs of eligible customers for broadcast tagihan
     * (approved, has paket, no unpaid tagihan)
     */
    public function getBroadcastIds()
    {
        try {
            $ids = Pelanggan::where('status', 'approve')
                ->whereNotNull('paket_id')
                ->whereDoesntHave('tagihans', function ($q) {
                    $q->where('status_pembayaran', 'belum bayar');
                })
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

        // Ambil pelanggan yang dipilih
        $pelanggan = Pelanggan::with('paket')
            ->where('status', 'approve')
            ->whereIn('id', $pelangganIds)
            ->whereNotIn('id', function ($query) {
                $query->select('pelanggan_id')
                    ->from('tagihans')
                    ->where('status_pembayaran', 'belum bayar');
            })
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

                Tagihan::create([
                    'pelanggan_id' => $p->id,
                    'paket_id' => $p->paket_id,
                    'harga' => $p->paket->harga,
                    'tanggal_mulai' => $request->tanggal_mulai,
                    'tanggal_berakhir' => $request->tanggal_berakhir,
                    'status_pembayaran' => 'belum bayar',
                ]);

                $successCount++;

                // Kirim push notification jika SID tersedia (dengan timeout pendek)
                if ($p->webpushr_sid) {
                    try {
                        $ch = curl_init('https://api.webpushr.com/v1/notification/send/sid');

                        $payload = [
                            'title' => 'Pemberitahuan untuk Anda',
                            'message' => "Halo {$p->nama_lengkap}, kami baru saja menerbitkan tagihan untuk Anda. Silakan cek detailnya.",
                            'target_url' => url('https://layanan.jernih.net.id/dashboard/customer/tagihan'),
                            'sid' => $p->webpushr_sid,
                        ];

                        $headers = [
                            'Content-Type: application/json',
                            'webpushrKey: 2ee12b373a17d9ba5f44683cb42d4279',
                            'webpushrAuthToken: 116294',
                        ];

                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_TIMEOUT, 3); // 3 second timeout
                        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2); // 2 second connection timeout

                        curl_exec($ch);
                        curl_close($ch);
                    } catch (\Throwable $e) {
                        // Ignore notification errors, continue with next customer
                        Log::warning('Failed to send webpushr notification', [
                            'pelanggan_id' => $p->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
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

        // HANYA EXPORT YANG LUNAS
        $filename = 'Tagihan_Lunas_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(
            new BayarExport($search, 'lunas'), // Status: lunas
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