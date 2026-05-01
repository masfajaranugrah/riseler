<?php

namespace App\Http\Controllers;

use App\Imports\PelangganImport;
use App\Models\Paket;
use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class MarketingController extends Controller
{
    private const PER_PAGE = 40;

    private const PROGRES_MENU = [
        'belum-progres' => Pelanggan::PROGRES_BELUM_DIPROSES,
        'tarik-kabel' => Pelanggan::PROGRES_TARIK_KABEL,
        'aktivasi' => Pelanggan::PROGRES_AKTIVASI,
        'registrasi' => Pelanggan::PROGRES_REGISTRASI,
    ];

    public function index(Request $request)
    {
        $page = $this->resolvePage($request);
        $query = $this->visibleMarketingQuery();

        $this->applyGlobalSearch($query, $request->input('search'));

        $pelanggan = $query->latest()->paginate(self::PER_PAGE, ['*'], 'page', $page);

        $statsQuery = $this->visibleMarketingQuery();
        $progressStats = [
            'belum_diproses' => (clone $statsQuery)
                ->where(function ($q) {
                    $q->whereNull('progres')
                        ->orWhere('progres', '')
                        ->orWhere('progres', Pelanggan::PROGRES_BELUM_DIPROSES);
                })
                ->count(),
            'tarik_kabel' => (clone $statsQuery)
                ->where('progres', Pelanggan::PROGRES_TARIK_KABEL)
                ->count(),
            'aktivasi' => (clone $statsQuery)
                ->where('progres', Pelanggan::PROGRES_AKTIVASI)
                ->count(),
            'registrasi' => (clone $statsQuery)
                ->where('progres', Pelanggan::PROGRES_REGISTRASI)
                ->count(),
        ];

        return view('content.apps.Marketing.pelanggan', compact('pelanggan', 'progressStats'));
    }

    public function getDataAprove()
    {
        $pelanggan = $this->visibleMarketingQuery()
            ->where('status', 'approve')
            ->get();

        $pelanggan = $pelanggan->values()->map(function ($item, $index) {
            $item->nomor_urut = $index + 1;
            return $item;
        });

        return response()->json([
            'data' => $pelanggan,
        ]);
    }

    public function status()
    {
        $pelanggan = $this->visibleMarketingQuery()
            ->latest()
            ->get();

        return view('content.apps.Marketing.status-pelanggan', compact('pelanggan'));
    }

    public function progres(Request $request)
    {
        return redirect()->route('marketing.progres.belum-progres');
    }

    public function progresBelum(Request $request)
    {
        return $this->renderProgresStage($request, 'belum-progres');
    }

    public function progresTarikKabel(Request $request)
    {
        return $this->renderProgresStage($request, 'tarik-kabel');
    }

    public function progresAktivasi(Request $request)
    {
        return $this->renderProgresStage($request, 'aktivasi');
    }

    public function progresRegistrasi(Request $request)
    {
        return $this->renderProgresStage($request, 'registrasi');
    }

    private function renderProgresStage(Request $request, string $stageKey)
    {
        $page = $this->resolvePage($request);
        $selectedStage = self::PROGRES_MENU[$stageKey] ?? Pelanggan::PROGRES_TARIK_KABEL;

        $query = Pelanggan::with(['paket', 'user'])
            ->whereHas('user', function ($q) {
                $q->where('role', 'marketing');
            });

        if (!$request->filled('search')) {
            if ($stageKey === 'belum-progres') {
                $query->where(function ($q) {
                    $q->where('progres', Pelanggan::PROGRES_BELUM_DIPROSES)
                        ->orWhereNull('progres')
                        ->orWhere('progres', '');
                });
            } else {
                $query->whereIn('status', ['pending', 'proses', 'survey', 'menunggu_instalasi', 'instalasi'])
                    ->where('progres', $selectedStage);
            }
        }

        $this->applyGlobalSearch($query, $request->input('search'));

        $pelanggan = $query->latest()->paginate(self::PER_PAGE, ['*'], 'page', $page);

        return view('content.apps.Marketing.progres', [
            'pelanggan' => $pelanggan,
            'selectedStage' => $selectedStage,
            'selectedStageKey' => $stageKey,
            'progressMenu' => self::PROGRES_MENU,
        ]);
    }

    public function approve(Request $request)
    {
        $page = $this->resolvePage($request);
        $query = $this->visibleMarketingQuery();

        if (!$request->filled('search')) {
            $query->where('status', 'approve');
        }

        $this->applyGlobalSearch($query, $request->input('search'));

        $pelanggan = $query->latest()->paginate(self::PER_PAGE, ['*'], 'page', $page);

        return view('content.apps.Marketing.approve', compact('pelanggan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'no_ktp' => 'nullable|string|max:50',
            'no_whatsapp' => 'nullable|string|max:50',
            'no_telp' => 'nullable|string|max:50',
            'alamat_jalan' => 'nullable|string|max:255',
            'rt' => 'nullable|string|max:5',
            'rw' => 'nullable|string|max:5',
            'desa' => 'nullable|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'kabupaten' => 'required|string|max:100',
            'provinsi' => 'nullable|string|max:100',
            'kode_pos' => 'nullable|string|max:10',
            'paket_id' => 'required|exists:pakets,id',

            'tanggal_mulai' => 'nullable|date',
            'tanggal_berakhir' => 'nullable|date',
            'deskripsi' => 'nullable|string',
            'progress_note' => 'nullable|string',
            'progres' => 'nullable|in:' . implode(',', Pelanggan::PROGRES_STAGES),
            'foto_ktp' => 'nullable|image|mimes:jpeg,png,jpg,webp,heic|max:10240',
        ]);

        DB::beginTransaction();

        try {
            $fotoKtpPath = null;
            
            if ($request->hasFile('foto_ktp')) {
                $fotoKtpPath = $request->file('foto_ktp')->store('foto_ktp', 'public');
            }

            $paket = Paket::findOrFail($validated['paket_id']);
            $tanggalMulai = $validated['tanggal_mulai'] ?? now();
            $tanggalBerakhir = $validated['tanggal_berakhir'] ?? now()->addDays($paket->masa_pembayaran);

            $prefixMap = [
                'Klaten' => 'PB-',
                'Gunung Kidul' => 'PB-GK-',
                'Boyolali' => 'PB-BY-'
            ];
            $basePrefix = $prefixMap[$validated['kabupaten'] ?? 'Klaten'] ?? 'PB-';

            do {
                $nomerId = $basePrefix . mt_rand(100000, 999999);
            } while (\App\Models\Pelanggan::where('nomer_id', $nomerId)->exists());

            Pelanggan::create([
                'user_id' => Auth::id(),
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
                'nomer_id' => $nomerId,
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_berakhir' => $tanggalBerakhir,
                'deskripsi' => $validated['deskripsi'] ?? null,
                'progress_note' => $validated['progress_note'] ?? null,
                // Simpan tahap awal sebagai NULL untuk kompatibilitas enum DB lama.
                'progres' => $this->normalizeProgresForStorage(Pelanggan::PROGRES_BELUM_DIPROSES),
                'foto_ktp' => $fotoKtpPath,
                'status' => Pelanggan::STATUS_PROSES,
            ]);

            DB::commit();

            return redirect()->route('marketing.pelanggan')->with('success', '? Pelanggan baru berhasil dibuat!');
            
        } catch (\Throwable $th) {
            DB::rollBack();
            
            if (isset($fotoKtpPath) && Storage::disk('public')->exists($fotoKtpPath)) {
                Storage::disk('public')->delete($fotoKtpPath);
            }

            return back()->with('error', '? Terjadi kesalahan: ' . $th->getMessage())->withInput();
        }
    }

    public function updateSid(Request $request, $nomerid)
    {
        $request->validate([
            'sid' => 'required|string',
        ]);

        $pelanggan = Pelanggan::where('nomer_id', $nomerid)->first();

        if (!$pelanggan) {
            return response()->json([
                'success' => false,
                'message' => 'Pelanggan tidak ditemukan',
            ], 404);
        }

        $pelanggan->update([
            'webpushr_sid' => $request->sid,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'SID berhasil disimpan',
            'data' => [
                'nomerid' => $pelanggan->nomer_id,
                'sid' => $request->sid,
            ],
        ]);
    }

    public function updateProgres(Request $request, $id)
    {
        $validated = $request->validate([
            'progres' => 'required|in:' . implode(',', Pelanggan::PROGRES_STAGES),
            'progress_note' => 'required|string|max:1000',
            'is_pending' => 'nullable|boolean',
        ]);

        $pelanggan = Pelanggan::findOrFail($id);
        $normalizedProgres = $this->normalizeProgresForStorage($validated['progres']);

        $newNote = trim($validated['progress_note']);
        $isPending = $request->boolean('is_pending');
        $newNote = preg_replace('/^\[PENDING\]\s*/i', '', $newNote);
        if ($isPending) {
            $newNote = '[PENDING] ' . $newNote;
        }
        $nextStatus = strtolower((string) $pelanggan->status);

        if (! in_array($nextStatus, [Pelanggan::STATUS_APPROVE, Pelanggan::STATUS_REJECT], true)) {
            // Kolom status di DB hanya mendukung: pending, approve, reject.
            // Status "proses" direpresentasikan lewat kolom progres (tahapan), bukan enum status.
            $nextStatus = Pelanggan::STATUS_PENDING;
        }

        $pelanggan->update([
            'progres' => $normalizedProgres,
            'progress_note' => $newNote,
            'status' => $nextStatus,
        ]);

        return redirect()->to($this->resolveSafeReturnUrl($request))
            ->with('success', "Update {$pelanggan->nama_lengkap} berhasil disimpan.");
    }

    public function redirectProgresUpdatePage(Request $request, $id)
    {
        return redirect()->route('marketing.pelanggan');
    }

    public function create()
    {
        $paket = Paket::all();
        return view('content.apps.Marketing.add-pelanggan', compact('paket'));
    }

    public function edit($id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        $paket = Paket::all();

        return view('content.apps.Marketing.edit-pelanggan', compact('pelanggan', 'paket'));
    }

    public function update(Request $request, $id)
    {
        $pelanggan = Pelanggan::findOrFail($id);

        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'no_ktp' => 'nullable|string|max:50',
            'no_whatsapp' => 'nullable|string|max:50',
            'no_telp' => 'nullable|string|max:50',
            'alamat_jalan' => 'nullable|string|max:255',
            'rt' => 'nullable|string|max:5',
            'rw' => 'nullable|string|max:5',
            'desa' => 'nullable|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'kabupaten' => 'nullable|string|max:100',
            'provinsi' => 'nullable|string|max:100',
            'kode_pos' => 'nullable|string|max:10',
            'paket_id' => 'required|exists:pakets,id',
            'nomer_id' => 'required|string|max:50|unique:pelanggans,nomer_id,' . $pelanggan->id,
            'tanggal_mulai' => 'nullable|date',
            'tanggal_berakhir' => 'nullable|date',
            'deskripsi' => 'nullable|string',
            'progress_note' => 'nullable|string',
            'progres' => 'nullable|in:' . implode(',', Pelanggan::PROGRES_STAGES),
            'foto_ktp' => 'nullable|image|mimes:jpeg,png,jpg,webp,heic|max:10240',
 
        ]);

        DB::beginTransaction();

        try {
            $paket = Paket::findOrFail($validated['paket_id']);
            $tanggalMulai = $validated['tanggal_mulai'] ?? now();
            $tanggalBerakhir = $validated['tanggal_berakhir'] ?? now()->parse($tanggalMulai)->addDays($paket->masa_pembayaran);

            if ($request->hasFile('foto_ktp')) {
                // Hapus foto lama jika ada
                if ($pelanggan->foto_ktp && Storage::disk('public')->exists($pelanggan->foto_ktp)) {
                    Storage::disk('public')->delete($pelanggan->foto_ktp);
                }
                
                // Upload foto baru
                $validated['foto_ktp'] = $request->file('foto_ktp')->store('foto_ktp', 'public');
            }

            $pelanggan->update([
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
                'progress_note' => $validated['progress_note'] ?? null,
                'progres' => $this->normalizeProgresForStorage(
                    $validated['progres'] ?? $pelanggan->progres ?? Pelanggan::PROGRES_BELUM_DIPROSES
                ),
                'foto_ktp' => $validated['foto_ktp'] ?? $pelanggan->foto_ktp,
 
            ]);

            DB::commit();

            $targetRoute = auth()->user()?->role === 'directur'
                ? 'directur.pelanggan'
                : 'marketing.pelanggan';

            return redirect()->route($targetRoute)->with('success', '? Data pelanggan berhasil diperbarui!');
            
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', '? Terjadi kesalahan: ' . $th->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        
        // Hapus foto KTP jika ada
        if ($pelanggan->foto_ktp && Storage::disk('public')->exists($pelanggan->foto_ktp)) {
            Storage::disk('public')->delete($pelanggan->foto_ktp);
        }
        
        $pelanggan->delete();

        return redirect()->back()->with('success', 'Data pelanggan berhasil dihapus.');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        Excel::import(new PelangganImport, $request->file('file'));

        return redirect()->route('marketing.pelanggan')->with('success', '? Data Excel berhasil diimport!');
    }

    private function visibleMarketingQuery()
    {
        return Pelanggan::with(['paket', 'user'])
            ->whereHas('user', function ($q) {
                $q->where('role', 'marketing');
            })
            ->where('nomer_id', 'LIKE', '%JMK-GK%');
    }

    private function resolvePage(Request $request): int
    {
        return max((int) $request->query('page', 1), 1);
    }

    private function normalizeProgresForStorage(?string $progres): ?string
    {
        if (blank($progres) || $progres === Pelanggan::PROGRES_BELUM_DIPROSES) {
            return null;
        }

        return $progres;
    }

    private function applyGlobalSearch($query, ?string $rawSearch): void
    {
        $search = trim((string) $rawSearch);
        if ($search === '') {
            return;
        }

        $query->where(function ($q) use ($search) {
            $q->where('nama_lengkap', 'like', "%{$search}%")
                ->orWhere('nomer_id', 'like', "%{$search}%")
                ->orWhere('no_whatsapp', 'like', "%{$search}%")
                ->orWhere('no_telp', 'like', "%{$search}%")
                ->orWhere('alamat_jalan', 'like', "%{$search}%")
                ->orWhere('desa', 'like', "%{$search}%")
                ->orWhere('kecamatan', 'like', "%{$search}%")
                ->orWhere('kabupaten', 'like', "%{$search}%")
                ->orWhere('provinsi', 'like', "%{$search}%")
                ->orWhere('kode_pos', 'like', "%{$search}%")
                ->orWhere('deskripsi', 'like', "%{$search}%")
                ->orWhere('progress_note', 'like', "%{$search}%")
                ->orWhere('status', 'like', "%{$search}%")
                ->orWhere('progres', 'like', "%{$search}%")
                ->orWhereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhereHas('paket', function ($pq) use ($search) {
                    $pq->where('nama_paket', 'like', "%{$search}%");
                });
        });
    }

    private function resolveSafeReturnUrl(Request $request): string
    {
        $default = route('marketing.pelanggan');
        $candidate = $request->input('return_url');

        if (! is_string($candidate) || blank($candidate)) {
            return $default;
        }

        $host = parse_url($candidate, PHP_URL_HOST);
        $path = parse_url($candidate, PHP_URL_PATH) ?? '';
        $sameHost = blank($host) || $host === $request->getHost();

        if (! $sameHost) {
            return $default;
        }

        if (! str_starts_with($path, '/dashboard/marketing/')) {
            return $default;
        }

        return $candidate;
    }
}
