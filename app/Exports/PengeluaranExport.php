<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PengeluaranExport implements WithMultipleSheets
{
    use Exportable;

    protected $pengeluaranGrouped;
    protected $bulan;
    protected $tahun;

    public function __construct($pengeluaranGrouped, $bulan, $tahun)
    {
        $this->pengeluaranGrouped = $pengeluaranGrouped;
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->pengeluaranGrouped as $group) {
            $sheets[] = new PengeluaranSheetExport($group, $this->bulan, $this->tahun);
        }

        return $sheets;
    }
}
