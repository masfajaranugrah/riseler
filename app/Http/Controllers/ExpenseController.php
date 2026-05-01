<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Income;
use App\Models\LedgerDaily;
use App\Models\Rekening;
use App\Exports\ExpenseMonthlyExport;
use App\Exports\ExpenseDateRangeExport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExpenseController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        
        // Filter Month & Year (Default: Current Month)
        $filterMonth = request('filter_month', $today->month);
        $filterYear = request('filter_year', $today->year);
        
        // Query dengan search filter DAN filter bulan/tahun
        $query = Expense::query();
        
        // Filter bulan dan tahun
        $query->whereMonth('tanggal_keluar', $filterMonth)
              ->whereYear('tanggal_keluar', $filterYear);
        
        // Search filter
        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('kode', 'like', '%' . $search . '%')
                  ->orWhere('kategori', 'like', '%' . $search . '%')
                  ->orWhere('keterangan', 'like', '%' . $search . '%');
            });
        }
        
        $expenses = $query->latest()->paginate(40);
        
        // Kategori untuk cards
        $kategori_list = [
            'BEBAN GAJI' => '202',
            'ALAT KANTOR HABIS PAKAI' => '203',
            'ALAT LOGISTIK' => '203',
            'ALAT TULIS KANTOR' => '203',
            'KONSUMSI' => '204',
            'BEBAN TRANSPORTASI' => '205',
            'BEBAN PERAWATAN' => '205',
            'BEBAN LAT (LISTRIK, AIR, TELEPON)' => '205',
            'BEBAN KEPERLUAN RUMAH TANGGA' => '205',
            'BEBAN TAGIHAN INTERNET' => '205',
            'BEBAN LAIN-LAIN' => '205',
            'BEBAN KOMITMEN / FEE' => '205',
            'BEBAN PRIVE' => '205',
            'BEBAN SRAGEN' => '205',
            'BEBAN GUNUNGKIDUL' => '205',
        ];
        
        // Hitung total per kategori untuk hari ini (tetap hari ini)
        $todayTotals = [];
        foreach ($kategori_list as $nama => $kode) {
            $todayTotals[$nama] = Expense::whereDate('tanggal_keluar', $today)
                ->where('kategori', $nama)
                ->sum('jumlah');
        }
        
        // Total keseluruhan hari ini
        $totalHariIni = Expense::whereDate('tanggal_keluar', $today)->sum('jumlah');

        // Total per tanggal (Summary Harian) - untuk bulan yang difilter
        $dailyTotals = Expense::selectRaw('DATE(tanggal_keluar) as date, SUM(jumlah) as total')
            ->whereMonth('tanggal_keluar', $filterMonth)
            ->whereYear('tanggal_keluar', $filterYear)
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        // TOTAL BULANAN - Total pengeluaran dalam bulan yang difilter
        $monthlyTotal = Expense::whereMonth('tanggal_keluar', $filterMonth)
            ->whereYear('tanggal_keluar', $filterYear)
            ->sum('jumlah');
        
        // Label bulan untuk ditampilkan
        $monthLabel = Carbon::createFromDate($filterYear, $filterMonth, 1)->locale('id')->isoFormat('MMMM YYYY');

        return view('content.apps.Laba.keluar.keluar', compact(
            'expenses', 'kategori_list', 'todayTotals', 'totalHariIni', 
            'today', 'dailyTotals', 'filterMonth', 'filterYear', 
            'monthlyTotal', 'monthLabel'
        ));
    }

    public function create()
    {
        $kategori_default = [
            'BEBAN GAJI' => '202',
            'ALAT KANTOR HABIS PAKAI' => '203',
            'ALAT LOGISTIK' => '203',
            'ALAT TULIS KANTOR' => '203',
            'KONSUMSI' => '204',
            'BEBAN TRANSPORTASI' => '205',
            'BEBAN PERAWATAN' => '205',
            'BEBAN LAT (LISTRIK, AIR, TELEPON)' => '205',
            'BEBAN KEPERLUAN RUMAH TANGGA' => '205',
            'BEBAN TAGIHAN INTERNET' => '205',
            'BEBAN LAIN-LAIN' => '205',
            'BEBAN KOMITMEN / FEE' => '205',
            'BEBAN PRIVE' => '205',
            'BEBAN SRAGEN' => '205',
            'BEBAN GUNUNGKIDUL' => '205',
            'DLL (Lainnya)' => '206',
        ];

        $rekenings = Rekening::all();

        return view('content.apps.Laba.keluar.add-keluar', compact('kategori_default', 'rekenings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori' => 'required|string',
            'jumlah' => 'required|numeric',
            'keterangan' => 'nullable|string',
            'kategori_dll' => 'nullable|string',
            'tanggal_keluar' => 'required|date',
        ]);

        // Tentukan kategori final
        $kategori = str_contains($request->kategori, 'DLL') && $request->kategori_dll
            ? $request->kategori_dll
            : $request->kategori;

        // Generate kode
        $kode = $this->getKode($kategori);

        // Bersihkan format rupiah dari input jumlah
        $jumlahBersih = str_replace('.', '', $request->jumlah);

        // Parse tanggal keluar
        $tanggalKeluar = Carbon::parse($request->tanggal_keluar);

        Expense::create([
            'kategori' => $kategori,
            'jumlah' => $jumlahBersih,
            'keterangan' => $request->keterangan,
            'kode' => $kode,
            'tipe_pembayaran' => $request->tipe_pembayaran ?? 'cash',
            'tanggal_keluar' => $tanggalKeluar,
            'created_at' => $tanggalKeluar,
            'updated_at' => $tanggalKeluar,
        ]);

        // Update ledger
        $this->updateLedger($tanggalKeluar->toDateString());

        return redirect()->route('keluar.index')->with('success', 'Pengeluaran berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $expense = Expense::findOrFail($id);
        $kategori_default = [
            'BEBAN GAJI' => '202',
            'ALAT KANTOR HABIS PAKAI' => '203',
            'ALAT LOGISTIK' => '203',
            'ALAT TULIS KANTOR' => '203',
            'KONSUMSI' => '204',
            'BEBAN TRANSPORTASI' => '205',
            'BEBAN PERAWATAN' => '205',
            'BEBAN LAT (LISTRIK, AIR, TELEPON)' => '205',
            'BEBAN KEPERLUAN RUMAH TANGGA' => '205',
            'BEBAN TAGIHAN INTERNET' => '205',
            'BEBAN LAIN-LAIN' => '205',
            'BEBAN KOMITMEN / FEE' => '205',
            'BEBAN PRIVE' => '205',
            'BEBAN SRAGEN' => '205',
            'BEBAN GUNUNGKIDUL' => '205',
            'DLL (Lainnya)' => '206',
        ];
        
        $rekenings = Rekening::all();

        return view('content.apps.Laba.keluar.edit-keluar', compact('expense', 'kategori_default', 'rekenings'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kategori' => 'required|string',
            'jumlah' => 'required|numeric',
            'keterangan' => 'nullable|string',
            'kategori_dll' => 'nullable|string',
            'tanggal_keluar' => 'required|date',
        ]);

        $expense = Expense::findOrFail($id);

        $kategori = str_contains($request->kategori, 'DLL') && $request->kategori_dll
            ? $request->kategori_dll
            : $request->kategori;

        // Simpan tanggal lama
        $tanggalSebelumnya = Carbon::parse($expense->tanggal_keluar)->toDateString();

        // Parse tanggal keluar baru
        $tanggalKeluarBaru = Carbon::parse($request->tanggal_keluar);

        // Bersihkan format rupiah dari input jumlah
        $jumlahBersih = str_replace('.', '', $request->jumlah);

        $expense->update([
            'kategori' => $kategori,
            'jumlah' => $jumlahBersih,
            'keterangan' => $request->keterangan,
            'tipe_pembayaran' => $request->tipe_pembayaran ?? 'cash',
            'tanggal_keluar' => $tanggalKeluarBaru,
        ]);

        // Update ledger untuk tanggal lama dan tanggal baru
        $this->updateLedger($tanggalSebelumnya);
        $this->updateLedger($tanggalKeluarBaru->toDateString());

        return redirect()->route('keluar.index')->with('success', 'Pengeluaran berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);
        $tanggal = Carbon::parse($expense->tanggal_keluar)->toDateString();
        $expense->delete();

        // Update ledger
        $this->updateLedger($tanggal);

        return redirect()->route('keluar.index')->with('success', 'Pengeluaran berhasil dihapus.');
    }

    /**
     * Update ledger harian otomatis sesuai tanggal
     */
    private function updateLedger($tanggal)
    {
        $ledger = LedgerDaily::firstOrCreate(['tanggal' => $tanggal]);

        $ledger->total_masuk = Income::whereDate('tanggal_masuk', $tanggal)->sum('jumlah');
        $ledger->total_keluar = Expense::whereDate('tanggal_keluar', $tanggal)->sum('jumlah');
        $ledger->saldo = $ledger->total_masuk - $ledger->total_keluar;

        $ledger->save();
    }

    private function getKode($kategori)
    {
        return match ($kategori) {
            'BEBAN GAJI' => '202',
            'ALAT KANTOR HABIS PAKAI' => '203',
            'ALAT LOGISTIK' => '203',
            'ALAT TULIS KANTOR' => '203',
            'KONSUMSI' => '204',
            'BEBAN TRANSPORTASI' => '205',
            'BEBAN PERAWATAN' => '205',
            'BEBAN LAT (LISTRIK, AIR, TELEPON)' => '205',
            'BEBAN KEPERLUAN RUMAH TANGGA' => '205',
            'BEBAN TAGIHAN INTERNET' => '205',
            'BEBAN LAIN-LAIN' => '205',
            'BEBAN KOMITMEN / FEE' => '205',
            'BEBAN PRIVE' => '205',
            'BEBAN SRAGEN' => '205',
            'BEBAN GUNUNGKIDUL' => '205',
            default => '206',
        };
    }

    /**
     * Export laporan bulanan ke Excel
     */
    public function exportMonthly(Request $request)
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);
        
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $expenses = Expense::whereBetween('tanggal_keluar', [$startDate, $endDate])
            ->orderBy('tanggal_keluar', 'asc')
            ->get();

        $kategoriDefinisi = [
            'BEBAN GAJI' => ['kode' => '202', 'jumlah' => 0, 'items' => []],
            'ALAT KANTOR HABIS PAKAI' => ['kode' => '203', 'jumlah' => 0, 'items' => []],
            'ALAT LOGISTIK' => ['kode' => '203', 'jumlah' => 0, 'items' => []],
            'ALAT TULIS KANTOR' => ['kode' => '203', 'jumlah' => 0, 'items' => []],
            'KONSUMSI' => ['kode' => '204', 'jumlah' => 0, 'items' => []],
            'BEBAN TRANSPORTASI' => ['kode' => '205', 'jumlah' => 0, 'items' => []],
            'BEBAN PERAWATAN' => ['kode' => '205', 'jumlah' => 0, 'items' => []],
            'BEBAN LAT (LISTRIK, AIR, TELEPON)' => ['kode' => '205', 'jumlah' => 0, 'items' => []],
            'BEBAN KEPERLUAN RUMAH TANGGA' => ['kode' => '205', 'jumlah' => 0, 'items' => []],
            'BEBAN TAGIHAN INTERNET' => ['kode' => '205', 'jumlah' => 0, 'items' => []],
            'BEBAN LAIN-LAIN' => ['kode' => '205', 'jumlah' => 0, 'items' => []],
            'BEBAN KOMITMEN / FEE' => ['kode' => '205', 'jumlah' => 0, 'items' => []],
            'BEBAN PRIVE' => ['kode' => '205', 'jumlah' => 0, 'items' => []],
            'BEBAN SRAGEN' => ['kode' => '205', 'jumlah' => 0, 'items' => []],
            'BEBAN GUNUNGKIDUL' => ['kode' => '205', 'jumlah' => 0, 'items' => []],
        ];

        $kategori206 = []; // Tampung kategori 206 (DLL) — nama bebas dari admin

        foreach ($expenses as $expense) {
            $kategori = strtoupper(trim($expense->kategori ?? ''));
            if (isset($kategoriDefinisi[$kategori])) {
                // Kategori 202-205 yang sudah terdefinisi
                $kategoriDefinisi[$kategori]['jumlah'] += $expense->jumlah;
                $kategoriDefinisi[$kategori]['items'][] = [
                    'tanggal'    => $expense->tanggal_keluar,
                    'keterangan' => $expense->keterangan ?? '-',
                    'jumlah'     => $expense->jumlah,
                ];
            } elseif (!empty($kategori)) {
                // Kategori 206 (DLL) — nama bebas yang diinput admin
                if (!isset($kategori206[$kategori])) {
                    $kategori206[$kategori] = ['kode' => '206', 'jumlah' => 0, 'items' => []];
                }
                $kategori206[$kategori]['jumlah'] += $expense->jumlah;
                $kategori206[$kategori]['items'][] = [
                    'tanggal'    => $expense->tanggal_keluar,
                    'keterangan' => $expense->keterangan ?? '-',
                    'jumlah'     => $expense->jumlah,
                ];
            }
        }

        $pengeluaranGrouped = [];
        foreach ($kategoriDefinisi as $nama => $data) {
            $pengeluaranGrouped[] = [
                'kode'     => $data['kode'],
                'kategori' => $nama,
                'jumlah'   => $data['jumlah'],
                'items'    => $data['items'],
            ];
        }

        // Tambahkan sheet 206 (DLL) — satu sheet per nama kategori unik
        foreach ($kategori206 as $nama => $data) {
            $pengeluaranGrouped[] = [
                'kode'     => '206',
                'kategori' => $nama,
                'jumlah'   => $data['jumlah'],
                'items'    => $data['items'],
            ];
        }

        $monthName = Carbon::createFromDate($year, $month, 1)->locale('id')->format('F_Y');
        $filename = 'Laporan_Pengeluaran_' . $monthName . '.xlsx';

        return Excel::download(new \App\Exports\PengeluaranExport($pengeluaranGrouped, $month, $year), $filename);
    }

    /**
     * Export laporan per rentang tanggal ke Excel
     */
    public function exportDateRange(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);
        
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        $filename = 'Detail_Pengeluaran_' . Carbon::parse($startDate)->format('d-m-Y') . '_sd_' . Carbon::parse($endDate)->format('d-m-Y') . '.xlsx';
        
        return Excel::download(new ExpenseDateRangeExport($startDate, $endDate), $filename);
    }
}
