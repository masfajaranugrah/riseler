<?php

namespace App\Exports;

use App\Models\Expense;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class ExpenseExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithEvents
{
    protected $tanggal;
    protected $bulan;
    protected $tahun;
    protected $search;
    protected $totalsByType = [];
    protected $totalAll = 0;

    public function __construct($tanggal = null, $bulan = null, $tahun = null, $search = null)
    {
        $this->tanggal = $tanggal;
        $this->bulan = $bulan;
        $this->tahun = $tahun;
        $this->search = $search;
        $this->buildTotals();
    }

    public function query()
    {
        $query = Expense::query();

        if ($this->search) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', '%' . $search . '%')
                    ->orWhere('kategori', 'like', '%' . $search . '%')
                    ->orWhere('keterangan', 'like', '%' . $search . '%');
            });
        }

        if ($this->tanggal) {
            $query->whereDay('tanggal_keluar', $this->tanggal);
        }

        if ($this->bulan) {
            $query->whereMonth('tanggal_keluar', $this->bulan);
        }

        if ($this->tahun) {
            $query->whereYear('tanggal_keluar', $this->tahun);
        }

        return $query
            ->orderBy('tipe_pembayaran', 'asc')
            ->orderBy('tanggal_keluar', 'asc');
    }

    public function headings(): array
    {
        return [
            'NO',
            'KODE',
            'KATEGORI',
            'TIPE PEMBAYARAN',
            'JUMLAH',
            'KETERANGAN',
            'TANGGAL KELUAR',
            'JAM KELUAR',
        ];
    }

    public function map($expense): array
    {
        static $no = 0;
        $no++;

        $tanggalKeluar = $expense->tanggal_keluar
            ? \Carbon\Carbon::parse($expense->tanggal_keluar)
            : null;

        return [
            $no,
            $expense->kode ?? '-',
            $expense->kategori ?? '-',
            strtoupper($expense->tipe_pembayaran ?? '-'),
            (float) ($expense->jumlah ?? 0),
            $expense->keterangan ?? '-',
            $tanggalKeluar ? $tanggalKeluar->format('d/m/Y') : '-',
            $tanggalKeluar ? $tanggalKeluar->format('H:i') : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();

        $sheet->getStyle("E2:E{$highestRow}")
            ->getNumberFormat()
            ->setFormatCode('"Rp "0');

        return [
            1 => [
                'font' => [
                    'bold' => true,
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $dataLastRow = $sheet->getHighestRow();
                $startRow = $dataLastRow + 2;

                $sheet->setCellValue("A{$startRow}", 'RINGKASAN TOTAL PER TIPE PEMBAYARAN');
                $sheet->mergeCells("A{$startRow}:D{$startRow}");
                $sheet->getStyle("A{$startRow}")->getFont()->setBold(true);

                $row = $startRow + 1;
                foreach ($this->totalsByType as $type => $total) {
                    $sheet->setCellValue("A{$row}", strtoupper($type));
                    $formattedTotal = 'Rp ' . number_format((float) $total, 0, ',', '.');
                    $sheet->setCellValueExplicit("B{$row}", $formattedTotal, DataType::TYPE_STRING);
                    $row++;
                }

                $sheet->setCellValue("A{$row}", 'Total Semua Tipe');
                $formattedTotalAll = 'Rp ' . number_format((float) $this->totalAll, 0, ',', '.');
                $sheet->setCellValueExplicit("B{$row}", $formattedTotalAll, DataType::TYPE_STRING);
                $sheet->getStyle("A{$row}:B{$row}")->getFont()->setBold(true);

                $sheet->getStyle("A{$startRow}:B{$row}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

                $sheet->getStyle("A{$startRow}:B{$row}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT);
            },
        ];
    }

    private function buildTotals(): void
    {
        $query = Expense::query();

        if ($this->search) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', '%' . $search . '%')
                    ->orWhere('kategori', 'like', '%' . $search . '%')
                    ->orWhere('keterangan', 'like', '%' . $search . '%');
            });
        }

        if ($this->tanggal) {
            $query->whereDay('tanggal_keluar', $this->tanggal);
        }

        if ($this->bulan) {
            $query->whereMonth('tanggal_keluar', $this->bulan);
        }

        if ($this->tahun) {
            $query->whereYear('tanggal_keluar', $this->tahun);
        }

        $this->totalsByType = $query
            ->selectRaw('COALESCE(tipe_pembayaran, "cash") as tipe, SUM(jumlah) as total')
            ->groupBy('tipe')
            ->pluck('total', 'tipe')
            ->toArray();

        $this->totalAll = array_sum($this->totalsByType);
    }
}
