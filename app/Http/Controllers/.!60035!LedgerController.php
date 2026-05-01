<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Income;
use App\Models\SaldoAwal;
use App\Models\Tagihan;
use App\Models\Paket;
use App\Models\Pelanggan;
use App\Exports\PembukuanTotalExport;
use App\Exports\BukuPembantuExport;
use App\Exports\PengeluaranExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class LedgerController extends Controller
{
    // Kategori Pengeluaran
    private $kategoriPengeluaran = [
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

    public function index(Request $request)
    {
        // Cek apakah ada filter bulan dan tahun
        $hasFilter = $request->has('bulan') && $request->has('tahun');

        if ($hasFilter) {
            // JIKA ADA FILTER: Tampilkan semua transaksi per hari dalam bulan tersebut
            $bulan = $request->get('bulan');
            $tahun = $request->get('tahun');

            $filteredData = $this->getBukuPembantuData($bulan, $tahun);
            $incomesData = $filteredData['incomes'];
            $expensesData = $filteredData['expenses'];
            
            $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();

            $filterMode = 'bulanan';


        } else {
            // DEFAULT (TANPA FILTER): Tampilkan transaksi hari ini saja
            $today = Carbon::today();
            $bulan = date('m');
            $tahun = date('Y');
            $startDate = $today;
            $endDate = $today->copy()->endOfDay();

            // Get pemasukan hari ini dari TAGIHAN LUNAS (pakai tanggal_mulai)
            $incomesData = Tagihan::where('status_pembayaran', 'lunas')
                ->whereDate('tanggal_mulai', $today)
                ->join('pakets', 'tagihans.paket_id', '=', 'pakets.id')
                ->selectRaw('DATE(tanggal_mulai) as tanggal, SUM(COALESCE(tagihans.harga, pakets.harga, 0)) as total_masuk')
                ->groupBy('tanggal')
                ->get()
                ->keyBy('tanggal');

            // Get expenses hari ini
            $expensesData = Expense::whereDate('tanggal_keluar', $today)
                ->selectRaw('DATE(tanggal_keluar) as tanggal, SUM(jumlah) as total_keluar')
                ->groupBy('tanggal')
                ->get()
                ->keyBy('tanggal');

            $filterMode = 'harian';
        }

        // Combine data untuk tabel
        $ledgerData = collect([]);
        $dates = $incomesData->keys()->merge($expensesData->keys())->unique()->sort();

        foreach ($dates as $date) {
            $ledgerData->push([
                'tanggal' => $date,
                'total_masuk' => $incomesData->has($date) ? $incomesData[$date]->total_masuk : 0,
                'total_keluar' => $expensesData->has($date) ? $expensesData[$date]->total_keluar : 0,
            ]);
        }

        // Calculate totals
        $todayTotalMasuk = $incomesData->sum('total_masuk');
        $todayTotalKeluar = $expensesData->sum('total_keluar');
        $todaySaldo = $todayTotalMasuk - $todayTotalKeluar;

        // Return JSON jika request AJAX
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'todayTotalMasuk' => $todayTotalMasuk,
                'todayTotalKeluar' => $todayTotalKeluar,
                'todaySaldo' => $todaySaldo,
                'ledgerData' => $ledgerData->values()->toArray(),
                'bulan' => $bulan,
                'tahun' => $tahun,
                'filterMode' => $filterMode
            ]);
        }

        return view('content.apps.Pembukuan.masuk.masuk', compact(
            'todayTotalMasuk',
            'todayTotalKeluar',
            'todaySaldo',
            'ledgerData',
            'startDate',
            'endDate',
            'bulan',
            'tahun',
            'filterMode'
        ));
    }

    public function keluar(Request $request)
    {
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));

        $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();

        $expenses = Expense::whereBetween('tanggal_keluar', [$startDate, $endDate])
            ->orderBy('tanggal_keluar', 'asc')
            ->get();

        $totalKeluar = $expenses->sum('jumlah');

        // Definisi kategori pengeluaran
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

        // Kelompokkan expenses ke masing-masing kategori
        foreach ($expenses as $expense) {
            $kategori = strtoupper(trim($expense->kategori ?? ''));
            if (isset($kategoriDefinisi[$kategori])) {
                $kategoriDefinisi[$kategori]['jumlah'] += $expense->jumlah;
                $kategoriDefinisi[$kategori]['items'][] = [
                    'tanggal' => $expense->tanggal_keluar,
                    'keterangan' => $expense->keterangan ?? '-',
                    'tipe_pembayaran' => $expense->tipe_pembayaran,
                    'jumlah' => $expense->jumlah,
                ];
            }
        }

        // Konversi ke array untuk view
        $pengeluaranGrouped = [];
        foreach ($kategoriDefinisi as $nama => $data) {
            $pengeluaranGrouped[] = [
                'kode' => $data['kode'],
                'kategori' => $nama,
                'jumlah' => $data['jumlah'],
                'items' => $data['items'],
            ];
        }

        $periodeLabel = Carbon::createFromDate($tahun, $bulan, 1)->locale('id')->isoFormat('MMMM YYYY');

        return view('content.apps.Pembukuan.keluar.keluar', compact(
            'pengeluaranGrouped', 'totalKeluar', 'bulan', 'tahun', 'periodeLabel'
        ));
    }

    public function exportExcelKeluar(Request $request)
    {
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));

        $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();

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

