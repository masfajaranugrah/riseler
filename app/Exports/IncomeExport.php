<?php

namespace App\Exports;

use App\Models\Income;
use Carbon\Carbon;
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
            return Carbon::parse($item->tanggal_masuk)->format('Y-m-d');
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
        return Income::query()
            ->when($this->month, function ($query) {
                $query->whereMonth('tanggal_masuk', $this->month);
            })
            ->when($this->year, function ($query) {
                $query->whereYear('tanggal_masuk', $this->year);
            })
            ->when($this->search, function ($query) {
                $search = $this->search;
                $query->where(function ($q) use ($search) {
                    $q->where('kode', 'like', '%' . $search . '%')
                      ->orWhere('kategori', 'like', '%' . $search . '%')
                      ->orWhere('keterangan', 'like', '%' . $search . '%');
                });
            })
            ->select([
                'kode',
                'kategori',
                'jumlah',
                'tipe_pembayaran as type_pembayaran',
                'tanggal_masuk',
                'keterangan as catatan',
            ])
            ->orderBy('tanggal_masuk', 'asc')
            ->get();
    }
}
