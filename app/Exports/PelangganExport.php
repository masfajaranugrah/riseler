<?php

namespace App\Exports;

use App\Models\Pelanggan;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PelangganExport implements WithMultipleSheets
{
    use Exportable;

    public function __construct()
    {
        // No month/year filter needed, exporting all time
    }

    public function sheets(): array
    {
        // Base condition: hanya pelanggan yang statusnya approve
        $baseCondition = function ($q) {
            $q->where('status', 'approve');
        };

        // Ambil SEMUA pelanggan (tanpa filter bulan/tahun) berdasarkan tanggal_mulai
        $pelangganAll = Pelanggan::query()
            ->with(['paket:id,nama_paket,kecepatan,harga'])
            ->where($baseCondition)
            ->whereNotNull('tanggal_mulai')
            ->orderBy('tanggal_mulai', 'asc')
            ->get();

        // Group by tanggal_mulai
        $groupedByDate = $pelangganAll->groupBy(function ($item) {
            return Carbon::parse($item->tanggal_mulai)->format('Y-m-d');
        });

        $sheets = [];

        // Buat sheet per tanggal
        foreach ($groupedByDate as $date => $items) {
            // Hitung total kumulatif: semua pelanggan dari awal sampai tanggal_mulai ini
            $totalSampaiTanggal = Pelanggan::where($baseCondition)
                ->whereNotNull('tanggal_mulai')
                ->where('tanggal_mulai', '<=', Carbon::parse($date)->endOfDay())
                ->count();

            $sheets[] = new PelangganDateSheetExport($date, $items, $totalSampaiTanggal);
        }

        // Jika tidak ada data, buat sheet kosong
        if (empty($sheets)) {
            $dateLabel = Carbon::createFromDate($this->year, $this->month, 1)->format('Y-m-d');
            $sheets[] = new PelangganDateSheetExport($dateLabel, collect(), 0);
        }

        return $sheets;
    }
}
