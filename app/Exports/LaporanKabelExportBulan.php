<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class LaporanKabelExportBulan implements WithMultipleSheets
{
    use Exportable;

    protected $month;
    protected $year;
    protected $wilayah;
    protected $search;

    public function __construct($month, $year, $wilayah = null, $search = '')
    {
        $this->month = $month;
        $this->year = $year;
        $this->wilayah = $wilayah;
        $this->search = $search;
    }

    public function sheets(): array
    {
        $sheets = [];

        // Validasi bulan dan tahun
        if (!is_numeric($this->month) || !is_numeric($this->year)) {
            return [];
        }

        $daysInMonth = Carbon::createFromDate($this->year, $this->month, 1)->daysInMonth;
        
        // Loop setiap tanggal di bulan itu dari 1 sampai akhir bulan
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::createFromDate($this->year, $this->month, $day)->format('Y-m-d');
            
            $sheets[] = new LaporanKabelExport(
                $date,
                $this->month,
                $this->year,
                $this->wilayah,
                $this->search,
                "Tgl " . $day // Judul sheet: Tgl 1, Tgl 2, dst.
            );
        }

        return $sheets;
    }
}
