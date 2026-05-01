<?php

namespace App\Http\Controllers;

use App\Imports\PelangganImport;
use App\Models\Paket;
use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PelangganExport;


class PelangganController extends Controller
{
    // index
public function exportExcel(Request $request)
{
    return Excel::download(
        new PelangganExport(),
        'data-pelanggan-keseluruhan.xlsx'
    );
}
    // public function getData()
    // {
    //     // Ambil hanya pelanggan dengan status pending
    //     $pelanggan = Pelanggan::with('paket')
    //         ->where('status', 'pending') // hanya pending
    //         ->get();

    //     // Tambahkan nomor urut seperti antrian
    //     $pelanggan = $pelanggan->values()->map(function ($item, $index) {
    //         $item->nomor_urut = $index + 1; // mulai dari 1

    //         return $item;
    //     });

    //     return response()->json([
    //         'data' => $pelanggan,
    //     ]);
    // }

    public function getDataAprove()
    {
        // Ambil hanya pelanggan dengan status pending
        $pelanggan = Pelanggan::with('paket')
            ->where('status', 'approve') // hanya pending
            ->get();

        // Tambahkan nomor urut seperti antrian
        $pelanggan = $pelanggan->values()->map(function ($item, $index) {
            $item->nomor_urut = $index + 1; // mulai dari 1

            return $item;
        });

        return response()->json([
            'data' => $pelanggan,
        ]);
    }

    public function updateSid(Request $request, $nomerid)
    {
        $request->validate([
            'sid' => 'required|string',
        ]);

        // Ambil pelanggan berdasarkan nomerid
        $pelanggan = Pelanggan::where('nomer_id', $nomerid)->first();

        if (! $pelanggan) {
            return response()->json([
                'success' => false,
                'message' => 'Pelanggan tidak ditemukan',
            ], 404);
        }

        // Update SID
        $pelanggan->update([
            'webpushr_sid' => $request->sid,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'SID berhasil disimpan',
            'data' => [
                'nomerid' => $pelanggan->nomerid,
                'sid' => $request->sid,
            ],
        ]);
    }

   public function status(Request $request) 
    {
        // ? Base query dengan eager loading (Hanya JMK-GK)
        $baseCondition = function($q) {
            $q->where(function($q2) {
                $q2->where('progres', Pelanggan::PROGRES_REGISTRASI)
                   ->orWhere('status', 'approve');
            })->where('nomer_id', 'LIKE', '%JMK-GK%');
        };

        $query = Pelanggan::with([
            'paket:id,nama_paket,harga,kecepatan,masa_pembayaran',
            'loginStatus' => function($q) {
                $q->latest()->limit(1);
            }
        ])->where($baseCondition);

        // ? Filter Status Active/Inactive
        if ($request->filled('status_filter')) {
            $statusFilter = $request->status_filter;
            
            if ($statusFilter === 'Active') {
                $query->whereHas('loginStatus', function($q) {
                    $q->where('is_active', true);
                });
            } elseif ($statusFilter === 'Inactive') {
                $query->where(function($q) {
                    $q->whereHas('loginStatus', function($subQ) {
                        $subQ->where('is_active', false);
                    })->orWhereDoesntHave('loginStatus');
                });
            }
        }

        // ? Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'LIKE', "%{$search}%")
                  ->orWhere('no_whatsapp', 'LIKE', "%{$search}%")
                  ->orWhere('nomer_id', 'LIKE', "%{$search}%")
                  ->orWhere('alamat_jalan', 'LIKE', "%{$search}%")
                  ->orWhereHas('paket', function($subQ) use ($search) {
                      $subQ->where('nama_paket', 'LIKE', "%{$search}%");
                  });
            });
        }

        // ? Pagination dengan query string
        $pelanggan = $query->latest()->paginate(40)->withQueryString();

        // ? Statistik untuk cards
        $statistics = [
            'total' => Pelanggan::where($baseCondition)->count(),
            'active' => Pelanggan::where($baseCondition)->whereHas('loginStatus', function($q) {
                $q->where('is_active', true);
            })->count(),
            'inactive' => Pelanggan::where($baseCondition)->where(function($q) {
                $q->whereDoesntHave('loginStatus', function($subQ) {
                    $subQ->where('is_active', true);
                })->orWhereDoesntHave('loginStatus');
            })->count(),
        ];

        return view('content.apps.Pelanggan.status', compact('pelanggan', 'statistics'));
    }

 
 

 
public function index(Request $request)
{
    $search = $request->get('search');
    $statusFilter = $request->get('status');

    $baseCondition = function($q) {
        $q->where(function($q2) {
            $q2->where('progres', Pelanggan::PROGRES_REGISTRASI)
               ->orWhere('status', 'approve');
        })->where('nomer_id', 'LIKE', '%JMK-GK%');
    };

    // Status counts (unfiltered by user search, but filtered by base condition)
    $countTotal = Pelanggan::where($baseCondition)->count();
    $countApprove = Pelanggan::where($baseCondition)->where('status', 'approve')->count();
    $countPending = Pelanggan::where($baseCondition)->whereIn('status', ['proses', 'pending'])->count();

    // Query with filters
    $pelanggan = Pelanggan::with([
            'user:id,name,email',
            'paket:id,nama_paket'
        ])
        ->where($baseCondition)
        ->select([
            'id', 'nomer_id', 'nama_lengkap', 'no_whatsapp',
            'alamat_jalan', 'rt', 'rw', 'kecamatan', 'kabupaten',
            'tanggal_mulai', 'foto_ktp', 'status', 'user_id', 'created_at',
            'deskripsi', 'progress_note', 'progres'
        ])
        // Filter by status if provided
        ->when($statusFilter, function($query) use ($statusFilter) {
            if ($statusFilter === 'proses') {
                return $query->whereIn('status', ['proses', 'pending']);
            }

            return $query->where('status', $statusFilter);
        })
        // Search filter
        ->when($search, function($query) use ($search) {
            return $query->where(function($q) use ($search) {
                $q->where('nomer_id', 'LIKE', "%{$search}%")
                  ->orWhere('nama_lengkap', 'LIKE', "%{$search}%")
                  ->orWhere('no_whatsapp', 'LIKE', "%{$search}%")
                  ->orWhere('alamat_jalan', 'LIKE', "%{$search}%")
                  ->orWhere('kecamatan', 'LIKE', "%{$search}%")
                  ->orWhere('kabupaten', 'LIKE', "%{$search}%")
                  ->orWhere('status', 'LIKE', "%{$search}%")
                  ->orWhere('rt', 'LIKE', "%{$search}%")
                  ->orWhere('rw', 'LIKE', "%{$search}%");
            });
        })
        ->latest()
        ->paginate(40)
        ->withQueryString();

    return view('content.apps.Pelanggan.pelanggan', compact(
        'pelanggan', 'countTotal', 'countApprove', 'countPending', 'statusFilter'
    ));
}

    // Halaman tambah pelanggan
    public function create()
    {
        $paket = Paket::all(); // get paket dari tabel paket

        return view('content.apps.Pelanggan.add-pelanggan', compact('paket'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'no_ktp' => 'nullable|string|max:50',
            'no_whatsapp' => 'nullable|string|max:50',
            'no_telp' => 'nullable|string|max:50',

            // Alamat lengkap
            'alamat_jalan' => 'nullable|string|max:255',
            'rt' => 'nullable|string|max:5',
            'rw' => 'nullable|string|max:5',
            'desa' => 'nullable|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'kabupaten' => 'nullable|string|max:100',
            'provinsi' => 'nullable|string|max:100',
            'kode_pos' => 'nullable|string|max:10',

            // Paket
            'paket_id' => 'required|exists:pakets,id',
            'nomer_id' => 'required|string|max:50|unique:pelanggans,nomer_id',

            // Tanggal
            'tanggal_mulai' => 'nullable|date',
            'tanggal_berakhir' => 'nullable|date',

            // Lain-lain
            'deskripsi' => 'nullable|string',
            'foto_ktp' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        DB::beginTransaction();

        try {

            // 2?? Upload foto KTP (jika ada)
            $fotoKtpPath = null;
            if ($request->hasFile('foto_ktp')) {
                $fotoKtpPath = $request->file('foto_ktp')->store('foto_ktp', 'public');
            }

            // 3?? Ambil paket & tentukan tanggal langganan
            $paket = Paket::findOrFail($validated['paket_id']);
            $tanggalMulai = $validated['tanggal_mulai'] ?? now();
            $tanggalBerakhir = $validated['tanggal_berakhir'] ?? now()->addDays($paket->masa_pembayaran);

            // 4?? Buat Pelanggan dengan user_id dari user baru
            Pelanggan::create([
                'nama_lengkap' => $validated['nama_lengkap'],
                'no_ktp' => $validated['no_ktp'] ?? null,
                'no_whatsapp' => $validated['no_whatsapp'] ?? null,
                'no_telp' => $validated['no_telp'] ?? null,

                'alamat_jalan' => $validated['alamat_jalan'] ?? null,
                'rt' => $validated['rt'] ?? null,
                'rw' => $validated['rw'] ?? null,
                'desa' => $validated['desa'] ?? null,
                'kecamatan' => $validated['kecamatan'] ?? null,
                'kabupaten' => $validated['kabupaten'] ?? null,
                'provinsi' => $validated['provinsi'] ?? null,
                'kode_pos' => $validated['kode_pos'] ?? null,

                'paket_id' => $paket->id,
                'nomer_id' => $validated['nomer_id'],
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_berakhir' => $tanggalBerakhir,

                'deskripsi' => $validated['deskripsi'] ?? null,
                'foto_ktp' => $fotoKtpPath,
                'status' => 'approve',
            ]);

            DB::commit();

            return redirect()->route('pelanggan')->with('success', '? Pelanggan baru dan akun login berhasil dibuat!');
        } catch (\Throwable $th) {
            DB::rollBack();

            return back()->with('error', '? Terjadi kesalahan: '.$th->getMessage());
        }
    }

    public function edit($id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        $paket = Paket::all();

        return view('content.apps.Pelanggan.edit-pelanggan', compact('pelanggan', 'paket'));
    }

    public function upload()
    {
        return view('content.apps.Pelanggan.upload');
    }

    // API Get Paket (optional untuk AJAX)

    public function update(Request $request, $id)
    {
        $pelanggan = Pelanggan::findOrFail($id);

        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'no_ktp' => 'nullable|string|max:50',
            'no_whatsapp' => 'nullable|string|max:50',
            'no_telp' => 'nullable|string|max:50',

            // Alamat lengkap
            'alamat_jalan' => 'nullable|string|max:255',
            'rt' => 'nullable|string|max:5',
            'rw' => 'nullable|string|max:5',
            'desa' => 'nullable|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'kabupaten' => 'nullable|string|max:100',
            'provinsi' => 'nullable|string|max:100',
            'kode_pos' => 'nullable|string|max:10',

            // Paket
            'paket_id' => 'required|exists:pakets,id',
            'nomer_id' => 'required|string|max:50|unique:pelanggans,nomer_id,'.$pelanggan->id,

            // Tanggal
            'tanggal_mulai' => 'nullable|date',
            'tanggal_berakhir' => 'nullable|date',

            // Lain-lain
            'deskripsi' => 'nullable|string',
            'foto_ktp' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'nullable|in:pending,approve,reject',
            'progress_note' => 'nullable|string|max:1000|required_if:status,pending',
        ]);

        $paket = Paket::findOrFail($validated['paket_id']);
        $tanggalMulai = $validated['tanggal_mulai'] ?? now();
        $tanggalBerakhir = $validated['tanggal_berakhir'] ?? now()->parse($tanggalMulai)->addDays($paket->masa_pembayaran);

        // Upload foto baru jika ada
        if ($request->hasFile('foto_ktp')) {
            // Hapus foto lama jika ada
            if ($pelanggan->foto_ktp && file_exists(storage_path('app/public/'.$pelanggan->foto_ktp))) {
                unlink(storage_path('app/public/'.$pelanggan->foto_ktp));
            }
            $pelanggan->foto_ktp = $request->file('foto_ktp')->store('foto_ktp', 'public');
        }

        $nextStatus = $validated['status'] ?? $pelanggan->status;
        $progressNote = $validated['progress_note'] ?? $pelanggan->progress_note;

        // Update data pelanggan
        $pelanggan->update([
            'nama_lengkap' => $validated['nama_lengkap'],
            'no_ktp' => $validated['no_ktp'] ?? null,
            'no_whatsapp' => $validated['no_whatsapp'] ?? null,
            'no_telp' => $validated['no_telp'] ?? null,

            // Alamat lengkap
            'alamat_jalan' => $validated['alamat_jalan'] ?? null,
            'rt' => $validated['rt'] ?? null,
            'rw' => $validated['rw'] ?? null,
            'desa' => $validated['desa'] ?? null,
            'kecamatan' => $validated['kecamatan'] ?? null,
            'kabupaten' => $validated['kabupaten'] ?? null,
            'provinsi' => $validated['provinsi'] ?? null,
            'kode_pos' => $validated['kode_pos'] ?? null,

            // Paket
            'paket_id' => $paket->id,
            'nomer_id' => $validated['nomer_id'],
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_berakhir' => $tanggalBerakhir,

            // Lain-lain
            'deskripsi' => $validated['deskripsi'] ?? null,
            'status' => $nextStatus,
            'progress_note' => $progressNote,
        ]);

        return redirect()->route('pelanggan')->with('success', '? Data pelanggan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        $pelanggan->delete();

        // Tambahkan notifikasi (opsional)
        return redirect()->back()->with('success', 'Data pelanggan berhasil dihapus.');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        Excel::import(new PelangganImport, $request->file('file'));

        return redirect()->route('pelanggan')->with('success', '? Data Excel berhasil diimport!');
    }
}
