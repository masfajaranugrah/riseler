<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;

class IncomeExportByDay implements WithMultipleSheets
{
    protected $bulan;
    protected $tahun;
    protected $search;

    public function __construct($bulan, $tahun, $search = null)
    {
        $this->bulan = (int) $bulan;
        $this->tahun = (int) $tahun;
        $this->search = $search;
    }

    public function sheets(): array
    {
        $sheets = [];
        $date = Carbon::create($this->tahun, $this->bulan, 1);
        $daysInMonth = $date->daysInMonth;

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $sheets[] = new class($day, $this->bulan, $this->tahun, $this->search) extends IncomeExport implements WithTitle {
                protected $titleDay;

                public function __construct($day, $bulan, $tahun, $search = null)
                {
                    $this->titleDay = (string) $day;
                    parent::__construct($day, $bulan, $tahun, $search);
                }

                public function title(): string
                {
                    return $this->titleDay;
                }
            };
        }

        return $sheets;
    }
}
