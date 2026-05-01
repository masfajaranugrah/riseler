<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Income;
use App\Models\LedgerDaily;
use App\Models\Rekening;
use App\Exports\IncomeExport;
use App\Models\Tagihan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class IncomeController extends Controller
{
    public function potonganPpn()
    {
        $today = Carbon::today();
        $filterMonth = request('filter_month', $today->month);
        $filterYear  = request('filter_year', $today->year);

        $query = Income::with('pelanggan:id,nomer_id,nama_lengkap')
            ->where('kategori', 'Potongan PPN')
            ->whereMonth('tanggal_masuk', $filterMonth)
            ->whereYear('tanggal_masuk', $filterYear)
            ->orderByDesc('tanggal_masuk');

        if (request('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('keterangan', 'like', '%' . $search . '%')
                    ->orWhere('kode', 'like', '%' . $search . '%')
                    ->orWhereHas('pelanggan', function ($p) use ($search) {
                        $p->where('nomer_id', 'like', '%' . $search . '%')
                            ->orWhere('nama_lengkap', 'like', '%' . $search . '%');
                    });
            });
        }

        $potonganPpn = $query->paginate(40)->withQueryString();
        $monthlyTotal = (clone $query)->sum('jumlah');
        $monthLabel = Carbon::createFromDate($filterYear, $filterMonth, 1)->locale('id')->isoFormat('MMMM YYYY');

        return view('content.apps.Laba.masuk.potongan-ppn', compact(
            'potonganPpn',
            'today',
            'filterMonth',
            'filterYear',
            'monthlyTotal',
            'monthLabel'
        ));
    }

    public function index()
    {
        $today = Carbon::today();

        // Filter Month & Year (Default: Current Month)
        $filterMonth = request('filter_month', $today->month);
        $filterYear  = request('filter_year', $today->year);

        // ── QUERY UTAMA: Tagihan Lunas (sumber pemasukan) ──────────────────
        $query = Tagihan::where('tagihans.status_pembayaran', 'lunas')
            ->whereMonth('tagihans.tanggal_pembayaran', $filterMonth)
            ->whereYear('tagihans.tanggal_pembayaran', $filterYear)
            ->leftJoin('pelanggans', 'pelanggans.id', '=', 'tagihans.pelanggan_id')
            ->leftJoin('pakets', 'pakets.id', '=', 'tagihans.paket_id')
            ->leftJoin('rekenings', 'rekenings.id', '=', 'tagihans.type_pembayaran')
            ->select([
                'tagihans.id',
                'tagihans.tanggal_pembayaran',
                'tagihans.harga',
                'tagihans.catatan',
                'tagihans.nama_paket',
                'pelanggans.nama_lengkap as nama_pelanggan',
                'pelanggans.nomer_id as nomer_id',
                DB::raw('COALESCE(rekenings.nama_bank, "Cash / Tunai") as tipe_pembayaran'),
                DB::raw('COALESCE(tagihans.harga, pakets.harga, 0) as jumlah'),
            ])
            ->orderByDesc('tagihans.tanggal_pembayaran');

        // Search filter
        if (request('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('pelanggans.nama_lengkap', 'like', '%' . $search . '%')
                  ->orWhere('pelanggans.nomer_id', 'like', '%' . $search . '%')
                  ->orWhere('tagihans.nama_paket', 'like', '%' . $search . '%')
                  ->orWhere('rekenings.nama_bank', 'like', '%' . $search . '%');
            });
        }

        $incomes = $query->paginate(20)->withQueryString();

        // ── REKAP PER BANK ───────────────────────────────────────────────────
        $bankTotals = Tagihan::where('tagihans.status_pembayaran', 'lunas')
            ->whereMonth('tagihans.tanggal_pembayaran', $filterMonth)
            ->whereYear('tagihans.tanggal_pembayaran', $filterYear)
            ->leftJoin('rekenings', 'rekenings.id', '=', 'tagihans.type_pembayaran')
            ->leftJoin('pakets', 'pakets.id', '=', 'tagihans.paket_id')
            ->selectRaw('COALESCE(rekenings.nama_bank, "Cash / Tunai") as nama_bank, SUM(COALESCE(tagihans.harga, pakets.harga, 0)) as total')
            ->groupByRaw('COALESCE(rekenings.nama_bank, "Cash / Tunai")')
            ->orderByDesc('total')
            ->get();

        // ── TOTAL HARIAN ─────────────────────────────────────────────────────
        $dailyTotals = Tagihan::where('status_pembayaran', 'lunas')
            ->whereMonth('tanggal_pembayaran', $filterMonth)
            ->whereYear('tanggal_pembayaran', $filterYear)
            ->leftJoin('pakets', 'pakets.id', '=', 'tagihans.paket_id')
            ->selectRaw('DATE(tagihans.tanggal_pembayaran) as date, SUM(COALESCE(tagihans.harga, pakets.harga, 0)) as total')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        // ── TOTAL BULANAN ─────────────────────────────────────────────────────
        $monthlyTotal = Tagihan::where('status_pembayaran', 'lunas')
            ->whereMonth('tanggal_pembayaran', $filterMonth)
            ->whereYear('tanggal_pembayaran', $filterYear)
            ->leftJoin('pakets', 'pakets.id', '=', 'tagihans.paket_id')
            ->selectRaw('SUM(COALESCE(tagihans.harga, pakets.harga, 0)) as total')
            ->value('total') ?? 0;

        $monthLabel = Carbon::createFromDate($filterYear, $filterMonth, 1)->locale('id')->isoFormat('MMMM YYYY');

        return view('content.apps.Laba.masuk.masuk', compact(
            'incomes', 'bankTotals', 'today',
            'dailyTotals', 'filterMonth', 'filterYear',
            'monthlyTotal', 'monthLabel'
        ));
    }

    public function export(Request $request)
    {
        $today = Carbon::today();
        $filterMonth = $request->input('filter_month', $today->month);
        $filterYear = $request->input('filter_year', $today->year);
        $search = $request->input('search');

        $filename = 'Laba_Masuk_' . Carbon::createFromDate($filterYear, $filterMonth, 1)->format('Y_m') . '.xlsx';

        return Excel::download(new IncomeExport($filterMonth, $filterYear, $search, false), $filename);
    }

    public function exportDedicated(Request $request)
    {
        $today = Carbon::today();
        $filterMonth = $request->input('filter_month', $today->month);
        $filterYear = $request->input('filter_year', $today->year);
        $search = $request->input('search');

        $filename = 'Laba_Masuk_Dedicated_' . Carbon::createFromDate($filterYear, $filterMonth, 1)->format('Y_m') . '.xlsx';

        return Excel::download(new IncomeExport($filterMonth, $filterYear, $search, true), $filename);
    }

    public function exportMonthly(Request $request)
    {
        $today = Carbon::today();
        $filterMonth = $request->input('filter_month', $today->month);
        $filterYear = $request->input('filter_year', $today->year);

        $filename = 'Laba_Masuk_Bulanan_1_Sheet_' . Carbon::createFromDate($filterYear, $filterMonth, 1)->format('Y_m') . '.xlsx';

        return Excel::download(new \App\Exports\IncomeMonthlyExport($filterMonth, $filterYear), $filename);
    }


    public function create()
    {
        $kategori_default = ['Internet', 'Penjualan', 'Piutang', 'DLL'];

        return view('content.apps.Laba.masuk.add-masuk', compact('kategori_default'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'kategori' => 'required|string',
            'jumlah' => 'required|numeric',
            'keterangan' => 'nullable|string',
            'kategori_dll' => 'nullable|string', // untuk DLL input manual
            'tanggal_masuk' => 'nullable|date',   // bisa diisi tanggal bebas
        ]);

        // Tentukan kategori final
        $kategori = $request->kategori === 'DLL' && $request->kategori_dll
            ? $request->kategori_dll
            : $request->kategori;

        // Generate kode otomatis berdasarkan kategori
        $kode = $this->getKode($kategori);

        // Convert tanggal_masuk ke Carbon, default sekarang jika kosong
        $tanggalMasuk = $request->tanggal_masuk
            ? \Carbon\Carbon::parse($request->tanggal_masuk)
            : now();

        // Simpan income ke database
        $income = Income::create([
            'kategori' => $kategori,
            'jumlah' => $request->jumlah,
            'keterangan' => $request->keterangan,
            'kode' => $kode,
            'tanggal_masuk' => $tanggalMasuk,
            'created_at' => $tanggalMasuk,
            'updated_at' => $tanggalMasuk,
        ]);

        // Update ledger harian otomatis sesuai tanggal masuk
        $this->updateLedger($tanggalMasuk->toDateString());

        return redirect()->route('income.index')->with('success', 'Laba Masuk berhasil ditambahkan.');
    }

    /**
     * Update ledger harian otomatis sesuai tanggal
     */
    private function updateLedger($tanggal)
    {
        $ledger = LedgerDaily::firstOrCreate(['tanggal' => $tanggal]);

        $ledger->total_masuk = Income::whereDate('tanggal_masuk', $tanggal)->sum('jumlah');
        $ledger->total_keluar = Expense::whereDate('tanggal_keluar', $tanggal)->sum('jumlah'); // sesuaikan field tanggal keluar
        $ledger->saldo = $ledger->total_masuk - $ledger->total_keluar;

        $ledger->save();
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

    public function edit($id)
    {
        $income = Income::findOrFail($id);
        $kategori_default = ['Internet', 'Penjualan', 'Piutang', 'DLL'];

        return view('content.apps.Laba.masuk.edit-masuk', compact('income', 'kategori_default'));
    }

    public function update(Request $request, $id)
{
    $request->validate([
        'kategori' => 'required|string',
        'jumlah' => 'required|numeric',
        'keterangan' => 'nullable|string',
        'kategori_dll' => 'nullable|string',
        'tanggal_masuk' => 'nullable|date',
    ]);

    $income = Income::findOrFail($id);

    $kategori = $request->kategori === 'DLL' && $request->kategori_dll
        ? $request->kategori_dll
        : $request->kategori;

    // Simpan tanggal lama sebagai string
    $tanggalSebelumnya = Carbon::parse($income->tanggal_masuk)->toDateString();

    // Parse tanggal masuk baru
    $tanggalMasukBaru = $request->tanggal_masuk
        ? Carbon::parse($request->tanggal_masuk)
        : Carbon::parse($income->tanggal_masuk);

    $income->update([
        'kategori' => $kategori,
        'jumlah' => $request->jumlah,
        'keterangan' => $request->keterangan,
        'tanggal_masuk' => $tanggalMasukBaru,
    ]);

    // Update ledger untuk tanggal lama dan tanggal baru
    $this->updateLedger($tanggalSebelumnya);
    $this->updateLedger($tanggalMasukBaru->toDateString());

    return redirect()->route('income.index')->with('success', 'Laba Masuk berhasil diperbarui.');
}

 public function destroy($id)
{
    $income = Income::findOrFail($id);

    // Perbaiki: Parse tanggal_masuk ke Carbon terlebih dahulu
    $tanggal = Carbon::parse($income->tanggal_masuk)->toDateString();

    $income->delete();

    $this->updateLedger($tanggal);

    return redirect()->route('income.index')->with('success', 'Laba Masuk berhasil dihapus.');
}

}
