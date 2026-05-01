<?php

namespace App\Exports;

use App\Models\Tagihan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;

class IncomeMonthlyExport implements FromView
{
    use Exportable;

    protected $month;
    protected $year;

    public function __construct($month = null, $year = null)
    {
        $this->month = $month;
        $this->year = $year;
    }

    public function view(): View
    {
        // 1. Get All Income (Lunas) for the month
        $incomes = Tagihan::where('tagihans.status_pembayaran', 'lunas')
            ->when($this->month, function ($query) {
                $query->whereMonth('tagihans.tanggal_pembayaran', $this->month);
            })
            ->when($this->year, function ($query) {
                $query->whereYear('tagihans.tanggal_pembayaran', $this->year);
            })
            ->leftJoin('pelanggans', 'pelanggans.id', '=', 'tagihans.pelanggan_id')
            ->leftJoin('pakets', 'pakets.id', '=', 'tagihans.paket_id')
            ->leftJoin('rekenings', 'rekenings.id', '=', 'tagihans.type_pembayaran')
            ->select([
                'pelanggans.nomer_id as no_pelanggan',
                'pelanggans.nama_lengkap as nama_pelanggan',
                DB::raw('COALESCE(tagihans.harga, pakets.harga, 0) as jumlah'),
                'tagihans.nama_paket',
                'tagihans.tanggal_pembayaran',
                'tagihans.status_pembayaran',
                'tagihans.tanggal_mulai',
                'tagihans.tanggal_berakhir as jatuh_tempo',
                DB::raw('COALESCE(rekenings.nama_bank, "Cash / Tunai") as type_pembayaran'),
            ])
            ->orderBy('tagihans.tanggal_pembayaran', 'asc')
            ->get();

        // 2. Summary by Payment Method
        $bankTotals = Tagihan::where('tagihans.status_pembayaran', 'lunas')
            ->when($this->month, function ($query) {
                $query->whereMonth('tagihans.tanggal_pembayaran', $this->month);
            })
            ->when($this->year, function ($query) {
                $query->whereYear('tagihans.tanggal_pembayaran', $this->year);
            })
            ->leftJoin('pakets', 'pakets.id', '=', 'tagihans.paket_id')
            ->leftJoin('rekenings', 'rekenings.id', '=', 'tagihans.type_pembayaran')
            ->select([
                DB::raw('COALESCE(rekenings.nama_bank, "Cash / Tunai") as nama_bank'),
                DB::raw('SUM(COALESCE(tagihans.harga, pakets.harga, 0)) as total')
            ])
            ->groupBy(DB::raw('COALESCE(rekenings.nama_bank, "Cash / Tunai")'))
            ->get();

        $monthLabel = Carbon::createFromDate($this->year, $this->month, 1)->locale('id')->isoFormat('MMMM YYYY');

        return view('content.apps.Laba.masuk.export-monthly-excel', [
            'incomes' => $incomes,
            'bankTotals' => $bankTotals,
            'monthLabel' => $monthLabel,
            'exporter' => $this
        ]);
    }

    public function formatJenisTagihan($row): string
    {
        if (empty($row->tanggal_mulai)) {
            return '-';
        }

        $period = Carbon::parse($row->tanggal_mulai)
            ->locale('id')
            ->translatedFormat('F Y');

        $isLate = false;
        if (!empty($row->tanggal_pembayaran) && !empty($row->jatuh_tempo)) {
            $dueDate = Carbon::parse($row->jatuh_tempo);
            $paymentDate = Carbon::parse($row->tanggal_pembayaran);
            $isLate = $paymentDate->greaterThan($dueDate);
        }

        if ($row->status_pembayaran === 'lunas') {
            $label = $isLate ? 'Outstanding' : 'Pembayaran';
            return "{$label} {$period}";
        }

        if (in_array($row->status_pembayaran, ['belum bayar', 'proses_verifikasi'])) {
            return "Outstanding {$period}";
        }

        return "Pembayaran {$period}";
    }
}
