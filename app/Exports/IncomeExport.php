<?php

namespace App\Exports;

use App\Models\Tagihan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class IncomeExport implements WithMultipleSheets
{
    use Exportable;

    protected $month;
    protected $year;
    protected $search;
    protected $is_dedicated;

    public function __construct($month = null, $year = null, $search = null, $is_dedicated = null)
    {
        $this->month = $month;
        $this->year = $year;
        $this->search = $search;
        $this->is_dedicated = $is_dedicated;
    }

    public function sheets(): array
    {
        $items = $this->queryItems();
        $sheets = [];

        // 1. TAMBAHKAN SHEET SUMMARY DI POSISI PERTAMA
        $sheets[] = new IncomeSummarySheetExport($items, $this->month, $this->year);

        $groupedByDate = $items->groupBy(function ($item) {
            return Carbon::parse($item->tanggal_pembayaran)->format('Y-m-d');
        });

        foreach ($groupedByDate as $date => $rows) {
            $sheets[] = new IncomeDateSheetExport($date, $rows);
        }

        if (empty($sheets)) {
            $fallbackDate = Carbon::createFromDate($this->year ?: now()->year, $this->month ?: now()->month, 1)->format('Y-m-d');
            $sheets[] = new IncomeDateSheetExport($fallbackDate, collect());
        }

        return $sheets;
    }

    protected function queryItems(): Collection
    {
        return Tagihan::where('tagihans.status_pembayaran', 'lunas')
            ->when($this->month, function ($query) {
                $query->whereMonth('tagihans.tanggal_pembayaran', $this->month);
            })
            ->when($this->year, function ($query) {
                $query->whereYear('tagihans.tanggal_pembayaran', $this->year);
            })
            ->when($this->search, function ($query) {
                $search = $this->search;
                $query->where(function ($q) use ($search) {
                    $q->where('pelanggans.nama_lengkap', 'like', '%' . $search . '%')
                      ->orWhere('pelanggans.nomer_id', 'like', '%' . $search . '%')
                      ->orWhere('pelanggans.no_whatsapp', 'like', '%' . $search . '%')
                      ->orWhere('tagihans.nama_paket', 'like', '%' . $search . '%')
                      ->orWhere('rekenings.nama_bank', 'like', '%' . $search . '%');
                });
            })
            ->when($this->is_dedicated, function ($query) {
                $query->where(function($q) {
                    $q->where('tagihans.nama_paket', 'like', '%Dedicated%')
                      ->orWhere('pakets.nama_paket', 'like', '%Dedicated%');
                });
            })
            ->leftJoin('pelanggans', 'pelanggans.id', '=', 'tagihans.pelanggan_id')
            ->leftJoin('pakets', 'pakets.id', '=', 'tagihans.paket_id')
            ->leftJoin('rekenings', 'rekenings.id', '=', 'tagihans.type_pembayaran')
            ->select([
                'pelanggans.nomer_id as no_pelanggan',
                'pelanggans.nama_lengkap as nama_pelanggan',
                'pelanggans.no_whatsapp as no_whatsapp',
                DB::raw('COALESCE(tagihans.harga, pakets.harga, 0) as jumlah'),
                'tagihans.kecepatan',
                'tagihans.tanggal_mulai',
                'tagihans.tanggal_berakhir as jatuh_tempo',
                'tagihans.status_pembayaran',
                'tagihans.tanggal_mulai',
                DB::raw('COALESCE(rekenings.nama_bank, "Cash / Tunai") as type_pembayaran'),
                'tagihans.tanggal_pembayaran',
                'tagihans.catatan',
            ])
            ->orderBy('tagihans.tanggal_pembayaran', 'asc')
            ->get();
    }
}
