<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Income;
use App\Models\LedgerDaily;
use App\Exports\IncomeExport;
use Carbon\Carbon;
use Illuminate\Http\Request;
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

        // ── QUERY UTAMA: Data Income (exclude Potongan PPN karena ada menu khusus) ──
        $query = Income::query()
            ->where('kategori', '!=', 'Potongan PPN')
            ->whereMonth('tanggal_masuk', $filterMonth)
            ->whereYear('tanggal_masuk', $filterYear)
            ->orderByDesc('tanggal_masuk');

        // Search filter
        if (request('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', '%' . $search . '%')
                  ->orWhere('kategori', 'like', '%' . $search . '%')
                  ->orWhere('keterangan', 'like', '%' . $search . '%')
                  ->orWhere('tipe_pembayaran', 'like', '%' . $search . '%');
            });
        }

        $incomes = $query->paginate(40)->withQueryString();

        // ── REKAP PER BANK ───────────────────────────────────────────────────
        $bankTotals = Income::query()
            ->where('kategori', '!=', 'Potongan PPN')
            ->whereMonth('tanggal_masuk', $filterMonth)
            ->whereYear('tanggal_masuk', $filterYear)
            ->selectRaw('COALESCE(tipe_pembayaran, "cash") as nama_bank, SUM(jumlah) as total')
            ->groupByRaw('COALESCE(tipe_pembayaran, "cash")')
            ->orderByDesc('total')
            ->get();

        // ── TOTAL HARIAN ─────────────────────────────────────────────────────
        $dailyTotals = Income::query()
            ->where('kategori', '!=', 'Potongan PPN')
            ->whereMonth('tanggal_masuk', $filterMonth)
            ->whereYear('tanggal_masuk', $filterYear)
            ->selectRaw('DATE(tanggal_masuk) as date, SUM(jumlah) as total')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        // ── TOTAL BULANAN ─────────────────────────────────────────────────────
        $monthlyTotal = Income::query()
            ->where('kategori', '!=', 'Potongan PPN')
            ->whereMonth('tanggal_masuk', $filterMonth)
            ->whereYear('tanggal_masuk', $filterYear)
            ->selectRaw('SUM(jumlah) as total')
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
            'kategori_dll' => 'required_if:kategori,DLL|nullable|string', // untuk DLL input manual
            'tanggal_masuk' => 'nullable|date',   // bisa diisi tanggal bebas
            'tipe_pembayaran' => 'required|string|in:cash,transfer',
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
            'tipe_pembayaran' => $request->tipe_pembayaran,
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
        'kategori_dll' => 'required_if:kategori,DLL|nullable|string',
        'tanggal_masuk' => 'nullable|date',
        'tipe_pembayaran' => 'required|string|in:cash,transfer',
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
        'tipe_pembayaran' => $request->tipe_pembayaran,
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
