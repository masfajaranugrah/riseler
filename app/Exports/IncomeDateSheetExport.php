<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class IncomeDateSheetExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, ShouldAutoSize, WithEvents
{
    protected $date;
    protected $items;
    protected $rowNumber = 0;

    public function __construct(string $date, Collection $items)
    {
        $this->date = $date;
        $this->items = $items;
    }

    public function collection()
    {
        return $this->items;
    }

    public function map($row): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $row->kode ?? '-',
            $row->kategori ?? '-',
            strtoupper($row->type_pembayaran ?? '-') ,
            (float) ($row->jumlah ?? 0),
            $row->tanggal_masuk ? Carbon::parse($row->tanggal_masuk)->format('d M Y') : '-',
            $row->tanggal_masuk ? Carbon::parse($row->tanggal_masuk)->format('H:i') : '-',
            $row->catatan ?? '-',
        ];
    }

    public function headings(): array
    {
        return [
            'NO',
            'KODE',
            'KATEGORI',
            'TYPE PEMBAYARAN',
            'JUMLAH PEMASUKAN',
            'TANGGAL',
            'JAM',
            'CATATAN',
        ];
    }

    public function title(): string
    {
        $date = Carbon::parse($this->date);
        $title = $date->format('d M');
        $title = str_replace(['*', ':', '/', '\\', '?', '[', ']'], '', $title);
        return substr($title, 0, 31);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'E' => ['numberFormat' => ['formatCode' => '#,##0']],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Calculate totals
                $totalCash = 0;
                $totalTransfer = 0;
                $totalPemasukan = 0;

                foreach ($this->items as $item) {
                    $jumlah = (float) ($item->jumlah ?? 0);
                    $totalPemasukan += $jumlah;

                    $tipe = strtolower($item->type_pembayaran ?? '');
                    if (str_contains($tipe, 'cash') || str_contains($tipe, 'tunai') || empty($tipe)) {
                        $totalCash += $jumlah;
                    } else {
                        $totalTransfer += $jumlah;
                    }
                }

                $sheet = $event->sheet->getDelegate();
                $lastRow = $this->rowNumber + 1; // +1 because of headings
                
                // Add two blank rows before summary
                $summaryStart = $lastRow + 2;

                // Title for Summary
                $sheet->mergeCells("D{$summaryStart}:E{$summaryStart}");
                $sheet->setCellValue("D{$summaryStart}", 'RINGKASAN PEMASUKAN');
                $sheet->getStyle("D{$summaryStart}")->getFont()->setBold(true);
                $sheet->getStyle("D{$summaryStart}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Total Cash
                $rowCash = $summaryStart + 1;
                $sheet->setCellValue("D{$rowCash}", 'TOTAL CASH');
                $sheet->setCellValue("E{$rowCash}", $totalCash);

                // Total Transfer
                $rowTf = $summaryStart + 2;
                $sheet->setCellValue("D{$rowTf}", 'TOTAL TRANSFER');
                $sheet->setCellValue("E{$rowTf}", $totalTransfer);

                // Total Semua
                $rowTotal = $summaryStart + 3;
                $sheet->setCellValue("D{$rowTotal}", 'TOTAL KESELURUHAN');
                $sheet->setCellValue("E{$rowTotal}", $totalPemasukan);

                // Apply styles to Summary section
                for ($i = $rowCash; $i <= $rowTotal; $i++) {
                    $sheet->getStyle("D{$i}")->getFont()->setBold(true);
                    $sheet->getStyle("E{$i}")->getNumberFormat()->setFormatCode('#,##0');
                }

                // Add yellow highlight to Total Keseluruhan
                $sheet->getStyle("D{$rowTotal}:E{$rowTotal}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFFFFF00']
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                    ]
                ]);
            }
        ];
    }
}
