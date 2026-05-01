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

        $kategori206 = []; // Tampung kategori 206 (DLL) — nama bebas dari admin

        foreach ($expenses as $expense) {
            $kategori = strtoupper(trim($expense->kategori ?? ''));
            if (isset($kategoriDefinisi[$kategori])) {
                // Kategori 202-205 yang sudah terdefinisi
                $kategoriDefinisi[$kategori]['jumlah'] += $expense->jumlah;
                $kategoriDefinisi[$kategori]['items'][] = [
                    'tanggal'     => $expense->tanggal_keluar,
                    'keterangan'  => $expense->keterangan ?? '-',
                    'tipe_pembayaran' => $expense->tipe_pembayaran,
                    'jumlah'      => $expense->jumlah,
                ];
            } elseif (!empty($kategori)) {
                // Kategori 206 (DLL) — nama kategori input admin, belum ada di daftar
                if (!isset($kategori206[$kategori])) {
                    $kategori206[$kategori] = ['kode' => '206', 'jumlah' => 0, 'items' => []];
                }
                $kategori206[$kategori]['jumlah'] += $expense->jumlah;
                $kategori206[$kategori]['items'][] = [
                    'tanggal'    => $expense->tanggal_keluar,
                    'keterangan' => $expense->keterangan ?? '-',
                    'tipe_pembayaran' => $expense->tipe_pembayaran,
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


        $monthName = Carbon::createFromDate($tahun, $bulan, 1)->locale('id')->format('F_Y');
        $filename = 'Pengeluaran_Administrasi_' . $monthName . '.xlsx';

        return Excel::download(new \App\Exports\PengeluaranExport($pengeluaranGrouped, $bulan, $tahun), $filename);
    }

    public function total(Request $request)
    {
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));
        
        // Get Saldo Awal (default to empty model if null to prevent crashes)
        $saldoAwal = SaldoAwal::getByPeriod($bulan, $tahun) ?? new SaldoAwal();
        
        // Get first and last day of the month for TAGIHAN/PEMASUKAN/PENGELUARAN
        $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();
        
        // UPDATED: Pengeluaran diambil dari BULAN YANG SAMA dengan filter
        // Jika filter Januari 2026, maka pengeluaran diambil dari Januari 2026
        $expenseStartDate = $startDate->copy();
        $expenseEndDate = $endDate->copy();
        $pengeluaranPeriodeLabel = Carbon::createFromDate($tahun, $bulan, 1)->locale('id')->isoFormat('MMMM YYYY');
        
        // Get all incomes for the month (bulan yang difilter)
        $incomes = Income::whereBetween('tanggal_masuk', [$startDate, $endDate])
            ->orderBy('tanggal_masuk', 'asc')
            ->get();
        
        // Get all expenses for THE SAME month (bukan bulan sebelumnya lagi)
        $expenses = Expense::whereBetween('tanggal_keluar', [$expenseStartDate, $expenseEndDate])
            ->orderBy('tanggal_keluar', 'asc')
            ->get();
        
        // ===== PEMASUKAN =====
        // UPDATED: Semua pemasukan dari tagihan BULAN YANG SAMA (bukan bulan sebelumnya)
        
        // 1. Pemasukan Dedicated Kotor = Tagihan Lunas + Paket Dedicated dari BULAN YANG SAMA
        $dedicatedData = $this->getDedicatedFromTagihan($expenseStartDate, $expenseEndDate);
        $pemasukanDedicatedKotor = $dedicatedData['kotor'];
        $jumlahDedicatedLunas = $dedicatedData['jumlah_lunas'];
        $jumlahDedicatedTotal = $dedicatedData['jumlah_total'];
        
        // 2. Pemasukan Home Net Kotor = Total Tagihan Lunas NON-Dedicated dari BULAN YANG SAMA
        $homeNetData = $this->getHomeNetFromTagihan($expenseStartDate, $expenseEndDate);
        $pemasukanHomeNetKotor = $homeNetData['kotor'];
        $jumlahHomeNetLunas = $homeNetData['jumlah_lunas'];
        $jumlahHomeNetTotal = $homeNetData['jumlah_total'];
        
        // 3. Potongan/Pengembalian
        // Potongan Dedicated = INPUT MANUAL dari SaldoAwal (atau 0 jika kosong)
        $potonganDedicated = $saldoAwal->pemasukan_dedicated_potongan ?? 0;
        
        // Potongan Home Net = OTOMATIS dari Total Pengeluaran "Beban Komitmen / Fee" BULAN SEBELUMNYA
        $bebanKomitmenFee = $expenses->filter(function($expense) {
            $kategori = strtoupper(trim($expense->kategori ?? ''));
            return str_contains($kategori, 'BEBAN KOMITMEN') || str_contains($kategori, 'FEE');
        })->sum('jumlah');
        $potonganHomeNet = $bebanKomitmenFee;
        
        // 4. Bersih = Kotor - Potongan
        $pemasukanDedicatedBersih = $pemasukanDedicatedKotor - $potonganDedicated;
        $pemasukanHomeNetBersih = $pemasukanHomeNetKotor - $potonganHomeNet;
        
        // 5. Registrasi - MANUAL input from SaldoAwal
        $pemasukanRegistrasi = $saldoAwal->pemasukan_registrasi ?? 0;
        
        // Total Pemasukan
        $totalPemasukan = $pemasukanRegistrasi + $pemasukanDedicatedBersih + $pemasukanHomeNetBersih;
        
        // ===== PENGELUARAN =====
        // Define all categories with their codes - show all individually
        $kategoriPengeluaran = [
            'BEBAN GAJI' => ['kode' => '202', 'jumlah' => 0],
            'ALAT KANTOR HABIS PAKAI' => ['kode' => '203', 'jumlah' => 0],
            'ALAT LOGISTIK' => ['kode' => '203', 'jumlah' => 0],
            'ALAT TULIS KANTOR' => ['kode' => '203', 'jumlah' => 0],
            'KONSUMSI' => ['kode' => '204', 'jumlah' => 0],
            'BEBAN TRANSPORTASI' => ['kode' => '205', 'jumlah' => 0],
            'BEBAN PERAWATAN' => ['kode' => '205', 'jumlah' => 0],
            'BEBAN LAT (LISTRIK, AIR, TELEPON)' => ['kode' => '205', 'jumlah' => 0],
            'BEBAN KEPERLUAN RUMAH TANGGA' => ['kode' => '205', 'jumlah' => 0],
            'BEBAN TAGIHAN INTERNET' => ['kode' => '205', 'jumlah' => 0],
            'BEBAN LAIN-LAIN' => ['kode' => '205', 'jumlah' => 0],
            'BEBAN KOMITMEN / FEE' => ['kode' => '205', 'jumlah' => 0],
            'BEBAN PRIVE' => ['kode' => '205', 'jumlah' => 0],
            'BEBAN SRAGEN' => ['kode' => '205', 'jumlah' => 0],
            'BEBAN GUNUNGKIDUL' => ['kode' => '205', 'jumlah' => 0],
        ];
        
        // Sum expenses by kategori field
        foreach ($expenses as $expense) {
            $kategori = strtoupper(trim($expense->kategori ?? ''));
            if (isset($kategoriPengeluaran[$kategori])) {
                $kategoriPengeluaran[$kategori]['jumlah'] += $expense->jumlah;
            }
        }
        
        // Convert to array format for view
        $pengeluaranData = [];
        foreach ($kategoriPengeluaran as $nama => $data) {
            $pengeluaranData[] = [
                'kode' => $data['kode'],
                'kategori' => $nama,
                'jumlah' => $data['jumlah']
            ];
        }
        
        $totalPengeluaran = $expenses->sum('jumlah');
        
        // ===== PIUTANG (Manual from DB) =====
        $piutangDedicated = $saldoAwal->piutang_dedicated ?? 0;
        $piutangHomeNet = $saldoAwal->piutang_homenet ?? 0;
        $piutangBulanSebelumnya = $saldoAwal->piutang_bulan_sebelumnya ?? 0;
        $piutangPeriodeSebelumnya = $saldoAwal->piutang_periode_sebelumnya ?? 0;
        $piutangTahunLalu = $saldoAwal->piutang_tahun_lalu ?? 0;
        $totalPiutang = $piutangDedicated + $piutangHomeNet + $piutangBulanSebelumnya + $piutangPeriodeSebelumnya + $piutangTahunLalu;
        
        // Label dinamis untuk piutang berdasarkan filter
        // UPDATED: Karena pengeluaran sekarang dari bulan yang sama, piutang juga dari bulan yang sama
        // Dedicated, HomeNet, Bulan Sebelumnya = bulan yang difilter (Jan 2026 jika filter Jan 2026)
        $piutangBulanLabel = $pengeluaranPeriodeLabel; // Sekarang sama dengan bulan filter
        
        // Periode sebelumnya: Jan - (bulan-1) tahun berjalan (jika filter Jan 2026 -> tidak ada/0)
        if ($bulan == '01') {
            // Jika filter Januari, tidak ada periode sebelumnya di tahun yang sama
            $piutangPeriodeLabel = "Jan - Des " . ($tahun - 1);
        } else {
            // Jika filter Feb atau selebihnya, periode = Jan - (bulan-1) tahun berjalan
            $bulanAkhirPeriode = (int)$bulan - 1;
            if ($bulanAkhirPeriode > 0) {
                $namaBulanAkhir = Carbon::createFromDate($tahun, $bulanAkhirPeriode, 1)->locale('id')->isoFormat('MMM');
                $piutangPeriodeLabel = "Jan - {$namaBulanAkhir} {$tahun}";
            } else {
                // Fallback
                $piutangPeriodeLabel = "Jan - Des " . ($tahun - 1);
            }
        }
        
        // Tahun lalu: tahun sebelumnya (2025 jika filter Jan 2026)
        if ($bulan == '01') {
            $tahunLalu = $tahun - 1; // Jika Jan 2026 -> 2025
            $piutangTahunLaluLabel = "Tahun = {$tahunLalu}";
        } else {
            $tahunLalu = $tahun - 1; // Jika Feb 2026 -> 2025
            $piutangTahunLaluLabel = "Tahun = " . ($tahunLalu - 1);
        }
        
        // Compile first month data
        $firstMonth = [
            'label' => Carbon::createFromDate($tahun, $bulan, 1)->locale('id')->isoFormat('MMMM YYYY'),
            'pengeluaranPeriodeLabel' => $pengeluaranPeriodeLabel, // Label periode pengeluaran (sekarang bulan yang sama)
            'saldoAwal' => $saldoAwal,
            'pemasukan' => [
                'registrasi' => $pemasukanRegistrasi,
                'dedicatedKotor' => $pemasukanDedicatedKotor,
                'potonganDedicated' => $potonganDedicated,
                'dedicatedBersih' => $pemasukanDedicatedBersih,
                'jumlahDedicatedLunas' => $jumlahDedicatedLunas,
                'jumlahDedicatedTotal' => $jumlahDedicatedTotal,
                'homeNetKotor' => $pemasukanHomeNetKotor,
                'potonganHomeNet' => $potonganHomeNet,
                'homeNetBersih' => $pemasukanHomeNetBersih,
                'jumlahHomeNetLunas' => $jumlahHomeNetLunas,
                'jumlahHomeNetTotal' => $jumlahHomeNetTotal,
            ],
            'totalPemasukan' => $totalPemasukan,
            'pengeluaran' => $pengeluaranData,
            'totalPengeluaran' => $totalPengeluaran,
            'piutang' => [
                'dedicated' => $piutangDedicated,
                'homeNet' => $piutangHomeNet,
                'bulanSebelumnya' => $piutangBulanSebelumnya,
                'periodeSebelumnya' => $piutangPeriodeSebelumnya,
                'tahunLalu' => $piutangTahunLalu,
                // Labels
                'dedicatedLabel' => "Piutang Dedicated {$pengeluaranPeriodeLabel}",
                'homeNetLabel' => "Piutang HomeNet {$pengeluaranPeriodeLabel}",
                'bulanSebelumnyaLabel' => "Piutang {$pengeluaranPeriodeLabel}",
                'periodeSebelumnyaLabel' => "Piutang {$piutangPeriodeLabel}",
                'tahunLaluLabel' => "Piutang {$piutangTahunLaluLabel}",
            ],
            'totalPiutang' => $totalPiutang,
        ];
        
        return view('content.apps.Pembukuan.total.total', compact('firstMonth', 'saldoAwal', 'bulan', 'tahun'));
    }

    public function bbpRingkasan(Request $request)
    {
        $bulan = (int) $request->get('bulan', date('m'));
        $tahun = (int) $request->get('tahun', date('Y'));

        $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();

        $pemasukan = Income::whereBetween('tanggal_masuk', [$startDate, $endDate])
            ->where('kategori', '!=', 'Potongan PPN')
            ->sum('jumlah');

        $potonganPpn = Income::whereBetween('tanggal_masuk', [$startDate, $endDate])
            ->where('kategori', 'Potongan PPN')
            ->sum('jumlah');

        $pendapatanKotor = $pemasukan + $potonganPpn;
        $pendapatanBersih = $pemasukan;
        $totalPengeluaran = Expense::whereBetween('tanggal_keluar', [$startDate, $endDate])->sum('jumlah');

        // Omset seharusnya = total biaya langganan seluruh pelanggan aktif
        $omsetSeharusnya = Pelanggan::query()
            ->where('status', 'approve')
            ->join('pakets', 'pelanggans.paket_id', '=', 'pakets.id')
            ->sum('pakets.harga');

        // Omset realisasi periode berjalan
        $omsetRealisasi = $pendapatanKotor;

        $piutang = max(0, $omsetSeharusnya - $omsetRealisasi);
        $rugiLaba = $pendapatanBersih - $totalPengeluaran;

        $periodeLabel = Carbon::createFromDate($tahun, $bulan, 1)->locale('id')->isoFormat('MMMM YYYY');

        return view('content.apps.Pembukuan.bbp.ringkasan', compact(
            'bulan',
            'tahun',
            'periodeLabel',
            'pemasukan',
            'potonganPpn',
            'pendapatanKotor',
            'pendapatanBersih',
            'totalPengeluaran',
            'omsetSeharusnya',
            'omsetRealisasi',
            'piutang',
            'rugiLaba'
        ));
    }
    /**
     * Export Buku Pembantu to Excel
     */
    public function exportExcelBukuPembantu(Request $request)
    {
        $bulanReq = $request->get('bulan', date('m'));
        $tahunReq = $request->get('tahun', date('Y'));

        $filteredData = $this->getBukuPembantuData($bulanReq, $tahunReq);
        $incomesData = $filteredData['incomes'];
        $expensesData = $filteredData['expenses'];

        // Combine data untuk tabel
        $ledgerData = collect([]);
        $dates = collect($incomesData->keys())->merge($expensesData->keys())->unique()->sort();

        foreach ($dates as $date) {
            $ledgerData->push([
                'tanggal' => $date,
                'total_masuk' => $incomesData->has($date) ? $incomesData[$date]->total_masuk : 0,
                'total_keluar' => $expensesData->has($date) ? $expensesData[$date]->total_keluar : 0,
            ]);
        }

        $totalMasuk = $incomesData->sum('total_masuk');
        $totalKeluar = $expensesData->sum('total_keluar');
        $saldo = $totalMasuk - $totalKeluar;

        $exportData = [
            'ledgerData' => $ledgerData->toArray(),
            'totalMasuk' => $totalMasuk,
            'totalKeluar' => $totalKeluar,
            'saldo' => $saldo
        ];

        $filename = 'Buku_Pembantu_' . Carbon::createFromDate($tahunReq, $bulanReq, 1)->format('F_Y') . '.xlsx';
        
        return Excel::download(new BukuPembantuExport($exportData, $bulanReq, $tahunReq), $filename);
    }

    private function getBukuPembantuData($bulan, $tahun)
    {
        $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
        $endDate   = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();
        // Siklus billing dimulai dari tanggal 26:
        // "Tagihan Februari" = tanggal_mulai 26 Jan s/d 25 Feb
        // Jika filter Maret ? outstanding Feb = tanggal_mulai 26 Jan - 25 Feb
        $twoMonthsAgo  = Carbon::createFromDate($tahun, $bulan, 1)->subMonths(2);
        $prevMonth     = Carbon::createFromDate($tahun, $bulan, 1)->subMonth();
        $prevStartDate = Carbon::createFromDate($twoMonthsAgo->year, $twoMonthsAgo->month, 26)->startOfDay();
        $prevEndDate   = Carbon::createFromDate($prevMonth->year, $prevMonth->month, 25)->endOfDay();

        // Batas tanggal: 1-25 = normal, 26-31 = outstanding terlambat
        $cutoffDate   = Carbon::createFromDate($tahun, $bulan, 25)->endOfDay();
        $after26Start = Carbon::createFromDate($tahun, $bulan, 26)->startOfDay();

        // ---- QUERY 1: Tanggal 1-25 ----
        // Tagihan FEBRUARI yang dibayar pada 1-25 Maret (pembayaran normal/awal bulan)
        $incomesRegular = Tagihan::where('status_pembayaran', 'lunas')
            ->whereBetween('tanggal_pembayaran', [$startDate, $cutoffDate])
            ->whereBetween('tanggal_mulai', [$prevStartDate, $prevEndDate])
            ->leftJoin('pakets', 'tagihans.paket_id', '=', 'pakets.id')
            ->selectRaw('DATE(tagihans.tanggal_pembayaran) as tanggal, SUM(COALESCE(tagihans.harga, pakets.harga, 0)) as total_masuk')
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->get()
            ->keyBy('tanggal');

        // ---- QUERY 2: Tanggal 26-31 ----
        // Tagihan FEBRUARI yang dibayar pada 26-31 Maret (outstanding terlambat)
        $incomesOutstanding = Tagihan::where('status_pembayaran', 'lunas')
            ->whereBetween('tanggal_pembayaran', [$after26Start, $endDate])
            ->whereBetween('tanggal_mulai', [$prevStartDate, $prevEndDate])
            ->leftJoin('pakets', 'tagihans.paket_id', '=', 'pakets.id')
            ->selectRaw('DATE(tagihans.tanggal_pembayaran) as tanggal, SUM(COALESCE(tagihans.harga, pakets.harga, 0)) as total_masuk')
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->get()
            ->keyBy('tanggal');

        // Gabungkan: regular (1-25) + outstanding (26-31)
        // Tanggal yang muncul = tanggal_pembayaran (kapan pelanggan bayar)
        $incomesData = $incomesRegular;
        foreach ($incomesOutstanding as $tanggal => $item) {
            if ($incomesData->has($tanggal)) {
                $incomesData[$tanggal]->total_masuk += $item->total_masuk;
            } else {
                $incomesData->put($tanggal, $item);
            }
        }
        $incomesData = $incomesData->sortKeys();

        // Get expenses per hari dalam bulan yang dipilih
        $expensesData = Expense::whereBetween('tanggal_keluar', [$startDate, $endDate])
            ->selectRaw('DATE(tanggal_keluar) as tanggal, SUM(jumlah) as total_keluar')
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->get()
            ->keyBy('tanggal');

        return [
            'incomes'  => $incomesData,
            'expenses' => $expensesData,
        ];
    }

    /**
     * Export data to Excel
     */
    public function exportExcel(Request $request)
    {
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));
        
        // Get Saldo Awal
        $saldoAwal = SaldoAwal::getByPeriod($bulan, $tahun) ?? new SaldoAwal();
        
        // Get first and last day of the month for TAGIHAN/PEMASUKAN
        $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();
        
        // TUTUP BUKU: Pengeluaran diambil dari BULAN SEBELUMNYA
        $prevMonth = Carbon::createFromDate($tahun, $bulan, 1)->subMonth();
        $expenseStartDate = $prevMonth->copy()->startOfMonth();
        $expenseEndDate = $prevMonth->copy()->endOfMonth();
        $pengeluaranPeriodeLabel = $prevMonth->locale('id')->isoFormat('MMMM YYYY');
        
        // Get all incomes for the month (bulan yang difilter)
        $incomes = Income::whereBetween('tanggal_masuk', [$startDate, $endDate])->get();
        // Get all expenses for PREVIOUS month (tutup buku)
        $expenses = Expense::whereBetween('tanggal_keluar', [$expenseStartDate, $expenseEndDate])->get();
        
        // Calculate Pemasukan
        $dedicatedData = $this->getDedicatedFromTagihan($startDate, $endDate);
        $pemasukanDedicatedKotor = $dedicatedData['kotor'];
        $potonganDedicated = $saldoAwal->pemasukan_dedicated_potongan ?? 0;
        $pemasukanDedicatedBersih = $pemasukanDedicatedKotor - $potonganDedicated;
        $pemasukanRegistrasi = $saldoAwal->pemasukan_registrasi ?? 0;
        $pemasukanHomeNetKotor = $saldoAwal->pemasukan_homenet_kotor ?? 0;
        $potonganHomeNet = $saldoAwal->pemasukan_homenet_potongan ?? 0;
        $pemasukanHomeNetBersih = $saldoAwal->pemasukan_homenet_bersih ?? 0;
        $totalPemasukan = $pemasukanRegistrasi + $pemasukanDedicatedBersih + $pemasukanHomeNetBersih;
        
        // Calculate Pengeluaran
        $kategoriPengeluaran = [
            'BEBAN GAJI' => ['kode' => '202', 'jumlah' => 0],
            'ALAT KANTOR HABIS PAKAI' => ['kode' => '203', 'jumlah' => 0],
            'ALAT LOGISTIK' => ['kode' => '203', 'jumlah' => 0],
            'ALAT TULIS KANTOR' => ['kode' => '203', 'jumlah' => 0],
            'KONSUMSI' => ['kode' => '204', 'jumlah' => 0],
            'BEBAN TRANSPORTASI' => ['kode' => '205', 'jumlah' => 0],
            'BEBAN PERAWATAN' => ['kode' => '205', 'jumlah' => 0],
            'BEBAN LAT (LISTRIK, AIR, TELEPON)' => ['kode' => '205', 'jumlah' => 0],
            'BEBAN KEPERLUAN RUMAH TANGGA' => ['kode' => '205', 'jumlah' => 0],
            'BEBAN TAGIHAN INTERNET' => ['kode' => '205', 'jumlah' => 0],
            'BEBAN LAIN-LAIN' => ['kode' => '205', 'jumlah' => 0],
            'BEBAN KOMITMEN / FEE' => ['kode' => '205', 'jumlah' => 0],
            'BEBAN PRIVE' => ['kode' => '205', 'jumlah' => 0],
            'BEBAN KOS-KOSAN' => ['kode' => '205', 'jumlah' => 0],
            'BEBAN SRAGEN' => ['kode' => '205', 'jumlah' => 0],
            'BEBAN GUNUNGKIDUL' => ['kode' => '205', 'jumlah' => 0],
        ];
        
        foreach ($expenses as $expense) {
            $kategori = strtoupper(trim($expense->kategori ?? ''));
            if (isset($kategoriPengeluaran[$kategori])) {
                $kategoriPengeluaran[$kategori]['jumlah'] += $expense->jumlah;
            }
        }
        
        $pengeluaranData = [];
        foreach ($kategoriPengeluaran as $nama => $data) {
            if ($data['jumlah'] > 0) { // Only include non-zero items
                $pengeluaranData[] = [
                    'kode' => $data['kode'],
                    'kategori' => $nama,
                    'jumlah' => $data['jumlah']
                ];
            }
        }
        
        $totalPengeluaran = $expenses->sum('jumlah');
        
        // Piutang
        $piutangDedicated = $saldoAwal->piutang_dedicated ?? 0;
        $piutangHomeNet = $saldoAwal->piutang_homenet ?? 0;
        $totalPiutang = $piutangDedicated + $piutangHomeNet;
        
        // Compile data
        $data = [
            'label' => Carbon::createFromDate($tahun, $bulan, 1)->locale('id')->isoFormat('MMMM YYYY'),
            'saldoAwal' => $saldoAwal,
            'pemasukan' => [
                'registrasi' => $pemasukanRegistrasi,
                'dedicatedKotor' => $pemasukanDedicatedKotor,
                'potonganDedicated' => $potonganDedicated,
                'dedicatedBersih' => $pemasukanDedicatedBersih,
                'homeNetKotor' => $pemasukanHomeNetKotor,
                'potonganHomeNet' => $potonganHomeNet,
                'homeNetBersih' => $pemasukanHomeNetBersih,
            ],
            'totalPemasukan' => $totalPemasukan,
            'pengeluaran' => $pengeluaranData,
            'totalPengeluaran' => $totalPengeluaran,
            'piutang' => [
                'dedicated' => $piutangDedicated,
                'homeNet' => $piutangHomeNet,
            ],
            'totalPiutang' => $totalPiutang,
        ];
        
        $filename = 'Rugi_Laba_' . Carbon::createFromDate($tahun, $bulan, 1)->format('F_Y') . '.xlsx';
        
        return Excel::download(new PembukuanTotalExport($data, $bulan, $tahun), $filename);
    }
    
    /**
     * Get Dedicated income from Tagihan table
     * Finds tagihan where paket nama contains "dedicated" or "DAD" and status is "Lunas"
     * Uses tanggal_pembayaran to filter by payment date (when it became lunas)
     */
    private function getDedicatedFromTagihan($startDate, $endDate)
    {
        // Get paket IDs that contain "dedicated", "dadicated", or "DAD" in their name (case-insensitive)
        $dedicatedPaketIds = Paket::where('nama_paket', 'LIKE', '%dedicated%')
            ->orWhere('nama_paket', 'LIKE', '%dadicated%')
            ->orWhere('nama_paket', 'LIKE', '%dad%')
            ->pluck('id');
        
        // Get tagihan with dedicated paket that were PAID (lunas) in the period
        // Using tanggal_pembayaran to filter by when payment was made
        $paidDedicatedTagihan = Tagihan::whereIn('paket_id', $dedicatedPaketIds)
            ->where('status_pembayaran', 'Lunas')
            ->whereBetween('tanggal_pembayaran', [$startDate, $endDate])
            ->get();
        
        // Get total count of dedicated tagihan in the period (regardless of payment status)
        $allDedicatedTagihan = Tagihan::whereIn('paket_id', $dedicatedPaketIds)
            ->whereBetween('tanggal_pembayaran', [$startDate, $endDate])
            ->get();
        
        // Calculate totals
        $kotor = 0;
        foreach ($paidDedicatedTagihan as $tagihan) {
            if ($tagihan->paket) {
                $kotor += $tagihan->paket->harga;
            }
        }
        
        return [
            'kotor' => $kotor,
            'potongan' => 0, 
            'jumlah_lunas' => $paidDedicatedTagihan->count(),
            'jumlah_total' => $allDedicatedTagihan->count(),
        ];
    }

    /**
     * Get Home Net income from Tagihan table
     * Finds tagihan where paket nama does NOT contain "dedicated" or "DAD" and status is "Lunas"
     * Uses tanggal_pembayaran to filter by payment date (when it became lunas)
     */
    private function getHomeNetFromTagihan($startDate, $endDate)
    {
        // Get paket IDs that contain "dedicated", "dadicated", or "DAD" in their name (to exclude)
        $dedicatedPaketIds = Paket::where('nama_paket', 'LIKE', '%dedicated%')
            ->orWhere('nama_paket', 'LIKE', '%dadicated%')
            ->orWhere('nama_paket', 'LIKE', '%dad%')
            ->pluck('id');
        
        // Get tagihan with NON-dedicated paket that were PAID (lunas) in the period
        // Using tanggal_pembayaran to filter by when payment was made
        $paidHomeNetTagihan = Tagihan::whereNotIn('paket_id', $dedicatedPaketIds)
            ->where('status_pembayaran', 'Lunas')
            ->whereBetween('tanggal_pembayaran', [$startDate, $endDate])
            ->get();
        
        // Get total count of home net tagihan in the period (regardless of payment status)
        $allHomeNetTagihan = Tagihan::whereNotIn('paket_id', $dedicatedPaketIds)
            ->whereBetween('tanggal_pembayaran', [$startDate, $endDate])
            ->get();
        
        // Calculate totals
        $kotor = 0;
        foreach ($paidHomeNetTagihan as $tagihan) {
            if ($tagihan->paket) {
                $kotor += $tagihan->paket->harga;
            }
        }
        
        return [
            'kotor' => $kotor,
            'potongan' => 0, 
            'jumlah_lunas' => $paidHomeNetTagihan->count(),
            'jumlah_total' => $allHomeNetTagihan->count(),
        ];
    }

    private function processMonthlyData($incomes, $expenses)
    {
        $groupedIncomes = $incomes->groupBy(function($val) {
            return Carbon::parse($val->tanggal_masuk)->format('Y-m');
        });

        $groupedExpenses = $expenses->groupBy(function($val) {
            return Carbon::parse($val->tanggal_keluar)->format('Y-m');
        });

        $allMonths = $groupedIncomes->keys()->merge($groupedExpenses->keys())->unique()->sort();

        $monthlyData = [];
        $saldoAkumulasi = 0;

        foreach($allMonths as $month) {
            $monthIncomes = $groupedIncomes->get($month, collect());
            $monthExpenses = $groupedExpenses->get($month, collect());

            // OMSET
            $omsetDedicated = $monthIncomes->where('kategori', 'Dedicated')->where('status', 'Lunas')->sum('jumlah');
            $omsetKotor = $monthIncomes->where('kategori', 'Home Net Kotor')->sum('jumlah');
            $potonganOmset = abs($monthIncomes->where('kategori', 'Potongan Home Net')->sum('jumlah'));
            $omsetHomeNetBersih = $omsetKotor - $potonganOmset;
            $totalOmset = $omsetDedicated + $omsetHomeNetBersih;

            // PEMASUKAN
            $pemasukanRegistrasi = $monthIncomes->where('kategori', 'Registrasi')->sum('jumlah');
            $pemasukanDedicated = $monthIncomes->where('kategori', 'Dedicated')->where('status', 'Lunas')->sum('jumlah');
            $pemasukanHomeNetKotor = $monthIncomes->where('kategori', 'Home Net Kotor')->sum('jumlah');
            $potonganHomeNet = abs($monthIncomes->where('kategori', 'Potongan Home Net')->sum('jumlah'));
            $pemasukanHomeNetBersih = $pemasukanHomeNetKotor - $potonganHomeNet;
            $totalPemasukan = $pemasukanRegistrasi + $pemasukanDedicated + $pemasukanHomeNetBersih;

            // PENGELUARAN
            $bebanGaji = $monthExpenses->where('kode_akun', '202')->sum('jumlah');
            $alatLogistik = $monthExpenses->where('kode_akun', '203')->sum('jumlah');

            $pengeluaranLainnya = $monthExpenses->whereNotIn('kode_akun', ['202', '203'])
                ->groupBy('kode_akun')
                ->map(function($group) {
                    return [
                        'kode' => $group->first()->kode_akun,
                        'nama' => $group->first()->nama_akun,
                        'jumlah' => $group->sum('jumlah')
                    ];
                })->values()->toArray();

            $totalPengeluaran = $monthExpenses->sum('jumlah');

            // PIUTANG
            $piutangDedicated = $monthIncomes->where('kategori', 'Dedicated')->where('status', 'Piutang')->sum('jumlah');
            $piutangHomeNet = $monthIncomes->where('kategori', 'Home Net')->where('status', 'Piutang')->sum('jumlah');
            $totalPiutang = $piutangDedicated + $piutangHomeNet;

            $saldoBersih = $totalPemasukan - $totalPengeluaran;

            $monthlyData[$month] = [
                'label' => Carbon::parse($month.'-01')->locale('id')->isoFormat('MMMM YYYY'),
                'saldoAwal' => $saldoAkumulasi,
                'omset' => [
                    'dedicated' => $omsetDedicated,
                    'kotor' => $omsetKotor,
                    'homeNetBersih' => $omsetHomeNetBersih,
                ],
                'totalOmset' => $totalOmset,
                'pemasukan' => [
                    'registrasi' => $pemasukanRegistrasi,
                    'dedicated' => $pemasukanDedicated,
                    'homeNetKotor' => $pemasukanHomeNetKotor,
                    'potonganHomeNet' => $potonganHomeNet,
                    'homeNetBersih' => $pemasukanHomeNetBersih,
                ],
                'totalPemasukan' => $totalPemasukan,
                'pengeluaran' => [
                    '202_bebanGaji' => $bebanGaji,
                    '203_alatLogistik' => $alatLogistik,
                    'lainnya' => $pengeluaranLainnya
                ],
                'totalPengeluaran' => $totalPengeluaran,
                'piutang' => [
                    'dedicated' => $piutangDedicated,
                    'homeNet' => $piutangHomeNet,
                ],
                'totalPiutang' => $totalPiutang,
                'saldoBersih' => $saldoBersih,
            ];

            $saldoAkumulasi += $saldoBersih;
        }

        return $monthlyData;
    }
}

