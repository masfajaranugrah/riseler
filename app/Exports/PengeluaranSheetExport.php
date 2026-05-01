<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class PengeluaranSheetExport implements WithTitle, WithEvents
{
    protected $group;
    protected $bulan;
    protected $tahun;

    public function __construct($group, $bulan, $tahun)
    {
        $this->group = $group;
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    public function title(): string
    {
        $title = $this->group['kode'] . ' - ' . $this->group['kategori'];
        // Remove invalid characters for excel sheet name
        $title = str_replace(['*', ':', '/', '\\', '?', '[', ']'], '', $title);
        // Max length is 31 characters
        if (strlen($title) > 31) {
            $title = substr($title, 0, 31);
        }
        return $title;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $periode = Carbon::createFromDate($this->tahun, $this->bulan, 1)->locale('id')->isoFormat('MMMM YYYY');
                
                // Judul
                $sheet->mergeCells('A1:E1');
                $sheet->setCellValue('A1', 'RINCIAN PENGELUARAN: ' . $this->group['kategori']);
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                
                $sheet->mergeCells('A2:E2');
                $sheet->setCellValue('A2', 'PERIODE: ' . strtoupper($periode));
                $sheet->getStyle('A2')->getFont()->setItalic(true);

                // Header tabel
                $row = 4;
                $sheet->setCellValue('A'.$row, 'NO');
                $sheet->setCellValue('B'.$row, 'TANGGAL');
                $sheet->setCellValue('C'.$row, 'METODE');
                $sheet->setCellValue('D'.$row, 'KETERANGAN');
                $sheet->setCellValue('E'.$row, 'JUMLAH');

                // Style Header
                $headerStyle = [
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFDBE5F1']
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                    ]
                ];
                $sheet->getStyle('A'.$row.':E'.$row)->applyFromArray($headerStyle);
                $row++;

                // Data items
                $startRow = $row;
                $no = 1;

                if (count($this->group['items']) > 0) {
                    foreach ($this->group['items'] as $item) {
                        $sheet->setCellValue('A'.$row, $no);
                        $sheet->setCellValue('B'.$row, Carbon::parse($item['tanggal'])->format('d M Y'));
                        $sheet->setCellValue('C'.$row, strtoupper($item['tipe_pembayaran'] ?? 'CASH'));
                        $sheet->setCellValue('D'.$row, $item['keterangan']);
                        $sheet->setCellValue('E'.$row, $item['jumlah']);
                        
                        $sheet->getStyle('A'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle('C'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle('E'.$row)->getNumberFormat()->setFormatCode('#,##0');
                        
                        $row++;
                        $no++;
                    }
                } else {
                    $sheet->mergeCells('A'.$row.':E'.$row);
                    $sheet->setCellValue('A'.$row, 'Tidak ada transaksi');
                    $sheet->getStyle('A'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $row++;
                }

                // Total
                $sheet->mergeCells('A'.$row.':D'.$row);
                $sheet->setCellValue('A'.$row, 'TOTAL ' . $this->group['kategori']);
                $sheet->setCellValue('E'.$row, $this->group['jumlah']);

                // Style Total
                $totalStyle = [
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFFFFF00']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_RIGHT
                    ]
                ];
                $sheet->getStyle('A'.$row.':E'.$row)->applyFromArray($totalStyle);
                $sheet->getStyle('E'.$row)->getNumberFormat()->setFormatCode('#,##0');

                // Border untuk Data + Total
                $endRow = $row;
                $bodyStyle = [
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                    ]
                ];
                $sheet->getStyle('A'.$startRow.':E'.$endRow)->applyFromArray($bodyStyle);

                // --- RINGKASAN CASH VS TF ---
                $totalCash = 0;
                $totalTransfer = 0;

                foreach ($this->group['items'] as $item) {
                    $tipe = strtolower($item['tipe_pembayaran'] ?? 'cash');
                    if (str_contains($tipe, 'cash') || str_contains($tipe, 'tunai') || empty($tipe)) {
                        $totalCash += $item['jumlah'];
                    } else {
                        $totalTransfer += $item['jumlah'];
                    }
                }

                $summaryStart = $row + 2;

                $sheet->mergeCells("D{$summaryStart}:E{$summaryStart}");
                $sheet->setCellValue("D{$summaryStart}", 'RINGKASAN METODE PEMBAYARAN');
                $sheet->getStyle("D{$summaryStart}")->getFont()->setBold(true);
                $sheet->getStyle("D{$summaryStart}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $rowCash = $summaryStart + 1;
                $sheet->setCellValue("D{$rowCash}", 'TOTAL CASH');
                $sheet->setCellValue("E{$rowCash}", $totalCash);

                $rowTf = $summaryStart + 2;
                $sheet->setCellValue("D{$rowTf}", 'TOTAL TRANSFER');
                $sheet->setCellValue("E{$rowTf}", $totalTransfer);

                $rowTotal = $summaryStart + 3;
                $sheet->setCellValue("D{$rowTotal}", 'TOTAL KESELURUHAN');
                $sheet->setCellValue("E{$rowTotal}", $this->group['jumlah']);

                for ($i = $rowCash; $i <= $rowTotal; $i++) {
                    $sheet->getStyle("D{$i}")->getFont()->setBold(true);
                    $sheet->getStyle("E{$i}")->getNumberFormat()->setFormatCode('#,##0');
                }

                $sheet->getStyle("D{$rowTotal}:E{$rowTotal}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFFFFF00']
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                    ]
                ]);
                // -----------------------------

                // Auto size columns
                $sheet->getColumnDimension('A')->setAutoSize(true);
                $sheet->getColumnDimension('B')->setAutoSize(true);
                $sheet->getColumnDimension('C')->setAutoSize(true);
                $sheet->getColumnDimension('D')->setWidth(50);
                $sheet->getColumnDimension('E')->setAutoSize(true);
            }
        ];
    }
}
