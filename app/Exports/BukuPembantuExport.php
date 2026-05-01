<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Font;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;

class BukuPembantuExport implements WithEvents
{
    protected $data;
    protected $bulan;
    protected $tahun;
    
    public function __construct($data, $bulan, $tahun)
    {
        $this->data = $data;
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }
    
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $row = 1;
                
                // Get period label
                $periodLabel = Carbon::createFromDate($this->tahun, $this->bulan, 1)->locale('id')->isoFormat('MMMM YYYY');
                $periodLabelUpper = strtoupper($periodLabel);
                
                // Set default style for whole sheet (font)
                $sheet->getStyle('A:D')->getFont()->setName('Calibri')->setSize(11);
                
                // Header (merged A:D)
                $sheet->mergeCells('A'.$row.':D'.$row);
                $sheet->setCellValue('A'.$row, 'BUKU BESAR PEMBANTU ' . $periodLabelUpper);
                $this->setStyle($sheet, 'A'.$row, [
                    'bold' => true, 
                    'align' => 'center', 
                    'size' => 16
                ]);
                $sheet->getRowDimension($row)->setRowHeight(30);
                $row++;
                
                // Blank row
                $sheet->mergeCells('A'.$row.':D'.$row);
                $row++;
                
                // Table Headers
                $headerRow = $row;
                $sheet->setCellValue('A'.$row, 'TANGGAL');
                $sheet->setCellValue('B'.$row, 'PEMASUKAN');
                $sheet->setCellValue('C'.$row, 'PENGELUARAN');
                $sheet->setCellValue('D'.$row, 'SALDO');
                $this->setStyle($sheet, 'A'.$row.':D'.$row, [
                    'bold' => false, // Not bold in screenshot
                    'align' => 'left'
                ]);
                $row++;
                
                // Accounting Format Code for IDR
                $accountingFormat = '_("Rp "* #,##0_);_("Rp "* \(#,##0\);_("Rp "* "-"??_);_(@_)';
                $numberFormat = '#,##0'; // plain thousands separator for SALDO or others
                
                // Saldo Awal Row
                $sheet->mergeCells('B'.$row.':C'.$row);
                $sheet->setCellValue('B'.$row, 'SALDO AWAL');
                $this->setStyle($sheet, 'B'.$row, ['align' => 'center']);
                
                $saldoAccumulation = 0; // Mulai dari 0
                $sheet->setCellValue('D'.$row, $saldoAccumulation);
                $sheet->getStyle('D'.$row)->getNumberFormat()->setFormatCode($numberFormat);
                $row++;
                
                // Table Data
                if (isset($this->data['ledgerData']) && count($this->data['ledgerData']) > 0) {
                    foreach ($this->data['ledgerData'] as $item) {
                        $tanggal = Carbon::parse($item['tanggal'])->format('d/m/Y');
                        
                        // -------- ROW 1: PEMASUKAN --------
                        // In the example, the date is printed and Pemasukan is logged.
                        $sheet->setCellValue('A'.$row, $tanggal);
                        $sheet->setCellValue('B'.$row, $item['total_masuk'] ?? 0);
                        
                        $saldoAccumulation += $item['total_masuk'] ?? 0;
                        $sheet->setCellValue('D'.$row, $saldoAccumulation);
                        
                        // Formatting Row 1
                        $this->setStyle($sheet, 'A'.$row, ['align' => 'right']); // Date right aligned based on image
                        $sheet->getStyle('B'.$row)->getNumberFormat()->setFormatCode($accountingFormat);
                        $sheet->getStyle('D'.$row)->getNumberFormat()->setFormatCode($numberFormat);
                        $row++;
                        
                        // -------- ROW 2: PENGELUARAN --------
                        // Next row has blank date, Pengeluaran in C, new Saldo in D
                        $sheet->setCellValue('C'.$row, $item['total_keluar'] ?? 0);
                        
                        $saldoAccumulation -= $item['total_keluar'] ?? 0;
                        $sheet->setCellValue('D'.$row, $saldoAccumulation);
                        
                        // Formatting Row 2
                        $sheet->getStyle('C'.$row)->getNumberFormat()->setFormatCode($accountingFormat);
                        $sheet->getStyle('D'.$row)->getNumberFormat()->setFormatCode($numberFormat);
                        $row++;
                    }
                }
                
                // Draw borders for the entire table
                $lastRow = $row - 1;
                $tableRange = 'A'.$headerRow.':D'.$lastRow;
                
                $sheet->getStyle($tableRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                
                // Column widths
                $sheet->getColumnDimension('A')->setWidth(18);
                $sheet->getColumnDimension('B')->setWidth(26);
                $sheet->getColumnDimension('C')->setWidth(26);
                $sheet->getColumnDimension('D')->setWidth(26);
                
                // Alignment settings
                // Column D (Saldo) right aligned
                $sheet->getStyle('D'.$headerRow.':D'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            },
        ];
    }
    
    private function setStyle($sheet, $cell, $options = [])
    {
        $style = $sheet->getStyle($cell);
        
        if (isset($options['bold'])) {
            $style->getFont()->setBold($options['bold']);
        }
        
        if (isset($options['size'])) {
            $style->getFont()->setSize($options['size']);
        }
        
        if (isset($options['align'])) {
            if ($options['align'] === 'center') {
                $style->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            } elseif ($options['align'] === 'right') {
                $style->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            } elseif ($options['align'] === 'left') {
                $style->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            }
        }
    }
}

