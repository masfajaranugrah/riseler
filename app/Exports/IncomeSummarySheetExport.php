<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class IncomeSummarySheetExport implements WithTitle, WithEvents
{
    protected $items;
    protected $month;
    protected $year;

    public function __construct(Collection $items, $month, $year)
    {
        $this->items = $items;
        $this->month = $month;
        $this->year = $year;
    }

    public function title(): string
    {
        return 'REKAP BULANAN';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $totalCash = 0;
                $totalTransfer = 0;
                $totalPemasukan = 0;

                // Group by Bank for breakdown
                $bankBreakdown = [];

                foreach ($this->items as $item) {
                    $jumlah = (float) ($item->jumlah ?? 0);
                    $totalPemasukan += $jumlah;

                    $tipe = trim($item->type_pembayaran ?? '');
                    $tipeLower = strtolower($tipe);

                    if (str_contains($tipeLower, 'cash') || str_contains($tipeLower, 'tunai') || empty($tipeLower)) {
                        $totalCash += $jumlah;
                    } else {
                        $totalTransfer += $jumlah;
                        
                        // Count per bank
                        $bankName = strtoupper($tipe);
                        if (!isset($bankBreakdown[$bankName])) {
                            $bankBreakdown[$bankName] = 0;
                        }
                        $bankBreakdown[$bankName] += $jumlah;
                    }
                }

                $sheet = $event->sheet->getDelegate();
                
                $periode = Carbon::createFromDate($this->year ?: now()->year, $this->month ?: now()->month, 1)->locale('id')->isoFormat('MMMM YYYY');

                // Title
                $sheet->mergeCells('B2:E2');
                $sheet->setCellValue('B2', 'REKAPITULASI PEMASUKAN LABA MASUK');
                $sheet->getStyle('B2')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('B2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->mergeCells('B3:E3');
                $sheet->setCellValue('B3', 'PERIODE: ' . strtoupper($periode));
                $sheet->getStyle('B3')->getFont()->setItalic(true);
                $sheet->getStyle('B3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Table Header
                $sheet->setCellValue('B5', 'METODE PEMBAYARAN');
                $sheet->setCellValue('C5', 'TOTAL (Rp)');
                $sheet->getStyle('B5:C5')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFDBE5F1']
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);

                // Content -> Cash
                $row = 6;
                $sheet->setCellValue('B' . $row, 'CASH / TUNAI');
                $sheet->setCellValue('C' . $row, $totalCash);
                $row++;

                // Content -> Bank breakdown
                foreach ($bankBreakdown as $bank => $jumlah) {
                    $sheet->setCellValue('B' . $row, 'TRANSFER ' . $bank);
                    $sheet->setCellValue('C' . $row, $jumlah);
                    $row++;
                }

                // Subtotal Transfer
                $sheet->setCellValue('B' . $row, 'SUBTOTAL TRANSFER');
                $sheet->setCellValue('C' . $row, $totalTransfer);
                $sheet->getStyle('B' . $row . ':C' . $row)->getFont()->setItalic(true);
                $sheet->getStyle('B' . $row . ':C' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF2F2F2');
                $row++;

                // Total
                $sheet->setCellValue('B' . $row, 'TOTAL KESELURUHAN');
                $sheet->setCellValue('C' . $row, $totalPemasukan);
                $sheet->getStyle('B' . $row . ':C' . $row)->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFFFFF00'] // Yellow
                    ]
                ]);

                // Styles for Data Rows
                $endRow = $row;
                $sheet->getStyle("B6:C{$endRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                    ]
                ]);
                $sheet->getStyle("C6:C{$endRow}")->getNumberFormat()->setFormatCode('#,##0');

                $sheet->getColumnDimension('B')->setAutoSize(true);
                $sheet->getColumnDimension('C')->setAutoSize(true);
            }
        ];
    }
}
