<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;

class BayarExportByDay implements WithMultipleSheets
{
    protected $status;
    protected $bulan;
    protected $tahun;

    public function __construct($status, $bulan, $tahun)
    {
        $this->status = $status;
        $this->bulan = (int) $bulan;
        $this->tahun = (int) $tahun;
    }

    public function sheets(): array
    {
        $sheets = [];
        $date = Carbon::create($this->tahun, $this->bulan, 1);
        $daysInMonth = $date->daysInMonth;

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $sheets[] = new class($this->status, $day, $this->bulan, $this->tahun) extends BayarExport implements WithTitle {
                protected $titleDay;

                public function __construct($status, $day, $bulan, $tahun)
                {
                    $this->titleDay = (string) $day;
                    parent::__construct($status, $day, $bulan, $tahun);
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
