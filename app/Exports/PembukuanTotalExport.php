<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Font;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;

class PembukuanTotalExport implements WithEvents
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
                
                // ===== HEADER =====
                $sheet->mergeCells('A'.$row.':F'.$row);
                $sheet->setCellValue('A'.$row, 'PT. JERNIH MULTI KOMUNIKASI');
                $this->setStyle($sheet, 'A'.$row, ['bold' => true, 'align' => 'center', 'size' => 14]);
                $row++;
                
                $sheet->mergeCells('A'.$row.':F'.$row);
                $sheet->setCellValue('A'.$row, 'RUGI LABA');
                $this->setStyle($sheet, 'A'.$row, ['bold' => true, 'align' => 'center', 'size' => 12]);
                $row++;
                
                $sheet->mergeCells('A'.$row.':F'.$row);
                $sheet->setCellValue('A'.$row, 'PERIODE ' . $periodLabelUpper);
                $this->setStyle($sheet, 'A'.$row, ['bold' => true, 'align' => 'center', 'size' => 11, 'underline' => true]);
                $row++;
                
                $sheet->mergeCells('A'.$row.':F'.$row);
                $prevMonth = Carbon::createFromDate($this->tahun, $this->bulan, 1)->subMonth()->locale('id')->isoFormat('MMMM YYYY');
                $sheet->setCellValue('A'.$row, 'PEMBAYARAN ' . strtoupper($prevMonth));
                $this->setStyle($sheet, 'A'.$row, ['bold' => true, 'align' => 'center', 'size' => 11, 'underline' => true]);
                $row += 2;
                
                // ===== SALDO AWAL / OMZET =====
                $sheet->setCellValue('A'.$row, 'SALDO AWAL');
                $this->setStyle($sheet, 'A'.$row, ['bold' => true]);
                $row++;
                
                $sheet->mergeCells('B'.$row.':E'.$row);
                $sheet->setCellValue('B'.$row, 'OMZET');
                $this->setStyle($sheet, 'B'.$row.':E'.$row, ['bold' => true, 'align' => 'center', 'fill' => 'FFFF00']);
                $row++;
                
                // Omzet items
                $sheet->setCellValue('B'.$row, 'TOTAL OMZET  INTERNET DEDICATED');
                $sheet->setCellValue('E'.$row, $this->formatNumber($this->data['saldoAwal']->omset_dedicated ?? 0));
                $this->setStyle($sheet, 'E'.$row, ['align' => 'right', 'color' => '0066CC']);
                $row++;
                
                $sheet->setCellValue('B'.$row, 'TOTAL OMZET HOME NET KOTOR');
                $sheet->setCellValue('E'.$row, $this->formatNumber($this->data['saldoAwal']->omset_homenet_kotor ?? 0));
                $this->setStyle($sheet, 'E'.$row, ['align' => 'right', 'color' => '0066CC']);
                $row++;
                
                $sheet->setCellValue('B'.$row, 'TOTAL OMZET HOME NET BERSIH');
                $sheet->setCellValue('E'.$row, $this->formatNumber($this->data['saldoAwal']->omset_homenet_bersih ?? 0));
                $this->setStyle($sheet, 'E'.$row, ['align' => 'right', 'color' => '0066CC']);
                $row++;
                
                $totalOmset = ($this->data['saldoAwal']->omset_dedicated ?? 0) + ($this->data['saldoAwal']->omset_homenet_bersih ?? 0);
                $sheet->setCellValue('B'.$row, 'TOTAL OMZET');
                $sheet->setCellValue('E'.$row, $this->formatNumber($totalOmset));
                $this->setStyle($sheet, 'B'.$row, ['bold' => true, 'fill' => 'FFFF00']);
                $this->setStyle($sheet, 'E'.$row, ['bold' => true, 'align' => 'right']);
                $row += 2;
                
                // ===== PEMASUKAN =====
                $sheet->mergeCells('B'.$row.':E'.$row);
                $sheet->setCellValue('B'.$row, 'PEMASUKAN');
                $this->setStyle($sheet, 'B'.$row.':E'.$row, ['bold' => true, 'align' => 'center', 'fill' => 'FFFF00']);
                $row++;
                
                // Registrasi
                $sheet->setCellValue('B'.$row, 'REGISTRASI');
                $sheet->setCellValue('F'.$row, $this->formatNumber($this->data['pemasukan']['registrasi'] ?? 0));
                $this->setStyle($sheet, 'F'.$row, ['align' => 'right', 'color' => '0066CC']);
                $row++;
                
                // Dedicated Kotor
                $sheet->setCellValue('B'.$row, 'PEMASUKAN DEDICATED KOTOR');
                $sheet->setCellValue('F'.$row, $this->formatNumber($this->data['pemasukan']['dedicatedKotor'] ?? 0));
                $this->setStyle($sheet, 'F'.$row, ['align' => 'right', 'color' => '0066CC']);
                $row++;
                
                // Potongan Dedicated
                $sheet->setCellValue('B'.$row, 'POTONGAN / PENGEMBALIAN');
                $row++;
                
                // Dedicated Bersih
                $sheet->setCellValue('B'.$row, 'PEMASUKAN DEDICATED BERSIH');
                $sheet->setCellValue('F'.$row, $this->formatNumber($this->data['pemasukan']['dedicatedBersih'] ?? 0));
                $this->setStyle($sheet, 'F'.$row, ['align' => 'right', 'color' => '0066CC']);
                $row++;
                
                // Home Net Kotor
                $sheet->setCellValue('B'.$row, 'PEMASUKAN HOME NET KOTOR');
                $sheet->setCellValue('F'.$row, $this->formatNumber($this->data['pemasukan']['homeNetKotor'] ?? 0));
                $this->setStyle($sheet, 'B'.$row, ['fill' => 'FFFF99']);
                $this->setStyle($sheet, 'F'.$row, ['align' => 'right', 'color' => '0066CC']);
                $row++;
                
                // Potongan Home Net
                $sheet->setCellValue('B'.$row, 'POTONGAN / PENGEMBALIAN');
                $sheet->setCellValue('E'.$row, $this->formatNumber($this->data['pemasukan']['potonganHomeNet'] ?? 0));
                $this->setStyle($sheet, 'E'.$row, ['align' => 'right']);
                $row++;
                
                // Home Net Bersih
                $sheet->setCellValue('B'.$row, 'PEMASUKAN HOMNET BERSIH');
                $sheet->setCellValue('E'.$row, $this->formatNumber($this->data['pemasukan']['homeNetBersih'] ?? 0));
                $this->setStyle($sheet, 'E'.$row, ['align' => 'right']);
                $row++;
                
                // Total Pemasukan
                $sheet->mergeCells('B'.$row.':E'.$row);
                $sheet->setCellValue('B'.$row, 'TOTAL PEMASUKAN');
                $sheet->setCellValue('F'.$row, $this->formatNumber($this->data['totalPemasukan'] ?? 0));
                $this->setStyle($sheet, 'B'.$row.':E'.$row, ['bold' => true, 'align' => 'center', 'fill' => 'FFFF00']);
                $this->setStyle($sheet, 'F'.$row, ['bold' => true, 'align' => 'right', 'color' => '0066CC']);
                $row += 2;
                
                // ===== PENGELUARAN =====
                $sheet->mergeCells('B'.$row.':E'.$row);
                $sheet->setCellValue('B'.$row, 'PENGELUARAN');
                $this->setStyle($sheet, 'B'.$row.':E'.$row, ['bold' => true, 'align' => 'center', 'fill' => 'FFFF00']);
                $row++;
                
                // Color codes for kode
                $kodeColors = [
                    '202' => '92D050', // Green
                    '203' => 'FFC000', // Orange
                    '204' => '808080', // Gray
                    '205' => 'FFFFFF', // White (no fill)
                ];
                
                // Pengeluaran items
                if (isset($this->data['pengeluaran']) && is_array($this->data['pengeluaran'])) {
                    foreach ($this->data['pengeluaran'] as $item) {
                        $kode = $item['kode'] ?? '';
                        $sheet->setCellValue('A'.$row, $kode);
                        $sheet->setCellValue('B'.$row, $item['kategori'] ?? '');
                        $sheet->setCellValue('E'.$row, $this->formatNumber($item['jumlah'] ?? 0));
                        
                        // Set color for kode cell
                        if (isset($kodeColors[$kode]) && $kode != '205') {
                            $this->setStyle($sheet, 'A'.$row, ['fill' => $kodeColors[$kode]]);
                        }
                        $this->setStyle($sheet, 'E'.$row, ['align' => 'right']);
                        $row++;
                    }
                }
                
                // Total Pengeluaran
                $sheet->mergeCells('B'.$row.':E'.$row);
                $sheet->setCellValue('B'.$row, 'TOTAL PENGELUARAN');
                $sheet->setCellValue('F'.$row, $this->formatNumber($this->data['totalPengeluaran'] ?? 0));
                $this->setStyle($sheet, 'B'.$row.':E'.$row, ['bold' => true, 'align' => 'center', 'fill' => 'FFFF00']);
                $this->setStyle($sheet, 'F'.$row, ['bold' => true, 'align' => 'right', 'color' => '0066CC']);
                $row += 2;
                
                // ===== LABA =====
                $laba = ($this->data['totalPemasukan'] ?? 0) - ($this->data['totalPengeluaran'] ?? 0);
                $sheet->mergeCells('B'.$row.':E'.$row);
                $sheet->setCellValue('B'.$row, 'LABA');
                $sheet->setCellValue('F'.$row, $this->formatNumber($laba));
                $this->setStyle($sheet, 'B'.$row.':E'.$row, ['bold' => true, 'align' => 'center', 'fill' => '87CEEB']); // Light blue
                $this->setStyle($sheet, 'F'.$row, ['bold' => true, 'align' => 'right']);
                $row += 2;
                
                // ===== PIUTANG =====
                $sheet->mergeCells('B'.$row.':E'.$row);
                $sheet->setCellValue('B'.$row, 'PIUTANG');
                $this->setStyle($sheet, 'B'.$row.':E'.$row, ['bold' => true, 'align' => 'center', 'fill' => 'FF6B6B']); // Light red
                $row++;
                
                // Piutang Dedicated
                $sheet->setCellValue('B'.$row, 'PIUTANG DEDICATED ' . $periodLabelUpper);
                $sheet->setCellValue('D'.$row, '-');
                $sheet->setCellValue('E'.$row, $this->formatNumber($this->data['piutang']['dedicated'] ?? 0));
                $this->setStyle($sheet, 'E'.$row, ['align' => 'right', 'color' => '0066CC']);
                $row++;
                
                // Piutang Home Net
                $sheet->setCellValue('B'.$row, 'PIUTANG HOMENET ' . $periodLabelUpper);
                $sheet->setCellValue('D'.$row, '-');
                $sheet->setCellValue('E'.$row, $this->formatNumber($this->data['piutang']['homeNet'] ?? 0));
                $this->setStyle($sheet, 'E'.$row, ['align' => 'right', 'color' => '0066CC']);
                $row++;
                
                // Total Piutang Periode
                $sheet->setCellValue('B'.$row, 'PIUTANG ' . $periodLabelUpper);
                $sheet->setCellValue('D'.$row, '-');
                $sheet->setCellValue('E'.$row, $this->formatNumber($this->data['totalPiutang'] ?? 0));
                $this->setStyle($sheet, 'E'.$row, ['align' => 'right', 'color' => 'FF0000']); // Red
                $row += 2;
                
                // Total Piutang historical (example)
                $sheet->setCellValue('B'.$row, 'TOTAL PIUTANG ' . $periodLabelUpper);
                $sheet->setCellValue('E'.$row, $this->formatNumber($this->data['totalPiutang'] ?? 0));
                $this->setStyle($sheet, 'E'.$row, ['align' => 'right', 'color' => 'FF0000']); // Red
                $row++;
                
                // Set column widths
                $sheet->getColumnDimension('A')->setWidth(8);
                $sheet->getColumnDimension('B')->setWidth(40);
                $sheet->getColumnDimension('C')->setWidth(12);
                $sheet->getColumnDimension('D')->setWidth(8);
                $sheet->getColumnDimension('E')->setWidth(18);
                $sheet->getColumnDimension('F')->setWidth(18);
            },
        ];
    }
    
    private function setStyle($sheet, $cell, $options = [])
    {
        $style = $sheet->getStyle($cell);
        
        if (isset($options['bold']) && $options['bold']) {
            $style->getFont()->setBold(true);
        }
        
        if (isset($options['size'])) {
            $style->getFont()->setSize($options['size']);
        }
        
        if (isset($options['underline']) && $options['underline']) {
            $style->getFont()->setUnderline(true);
        }
        
        if (isset($options['align'])) {
            if ($options['align'] === 'center') {
                $style->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            } elseif ($options['align'] === 'right') {
                $style->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }
        }
        
        if (isset($options['fill'])) {
            $style->getFill()->setFillType(Fill::FILL_SOLID);
            $style->getFill()->getStartColor()->setARGB($options['fill']);
        }
        
        if (isset($options['color'])) {
            $style->getFont()->getColor()->setARGB($options['color']);
        }
    }
    
    private function formatNumber($value)
    {
        return number_format($value, 0, ',', '.');
    }
}
