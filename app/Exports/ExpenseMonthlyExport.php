<?php

namespace App\Exports;

use App\Models\Expense;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExpenseMonthlyExport implements FromCollection, WithHeadings, WithTitle, WithStyles, WithEvents, ShouldAutoSize
{
    protected $month;
    protected $year;
    protected $kategoriList;

    public function __construct($month, $year)
    {
        $this->month = $month;
        $this->year = $year;
        $this->kategoriList = [
            'BEBAN GAJI' => '202',
            'ALAT KANTOR HABIS PAKAI' => '203',
            'ALAT LOGISTIK' => '203',
            'ALAT TULIS KANTOR' => '203',
            'KONSUMSI' => '204',
            'BEBAN TRANSPORTASI' => '205',
            'BEBAN PERAWATAN' => '205',
            'BEBAN LAT (LISTRIK, AIR, TELEPON)' => '205',
            'BEBAN KEPERLUAN RUMAH TANGGA' => '205',
            'BEBAN TAGIHAN INTERNET' => '205',
            'BEBAN LAIN-LAIN' => '205',
            'BEBAN KOMITMEN / FEE' => '205',
            'BEBAN PRIVE' => '205',
            'BEBAN SRAGEN' => '205',
            'BEBAN GUNUNGKIDUL' => '205',
        ];
    }

    public function collection()
    {
        $daysInMonth = Carbon::createFromDate($this->year, $this->month, 1)->daysInMonth;
        $data = collect();

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::createFromDate($this->year, $this->month, $day);
            
            $row = [
                'no' => $day,
                'tanggal' => $date->format('d-m-Y'),
                'keterangan' => $this->getKeterangan($date),
            ];

            // Tambahkan kolom untuk setiap kategori
            foreach ($this->kategoriList as $nama => $kode) {
                $total = Expense::whereDate('tanggal_keluar', $date)
                    ->where('kategori', $nama)
                    ->sum('jumlah');
                $row[$nama] = $total > 0 ? $total : '-';
            }

            // Total hari ini
            $row['total'] = Expense::whereDate('tanggal_keluar', $date)->sum('jumlah');
            $row['total'] = $row['total'] > 0 ? $row['total'] : '-';

            $data->push($row);
        }

        // Tambah baris total
        $totalRow = [
            'no' => '',
            'tanggal' => 'TOTAL',
            'keterangan' => '',
        ];

        foreach ($this->kategoriList as $nama => $kode) {
            $totalRow[$nama] = Expense::whereMonth('tanggal_keluar', $this->month)
                ->whereYear('tanggal_keluar', $this->year)
                ->where('kategori', $nama)
                ->sum('jumlah');
        }
        $totalRow['total'] = Expense::whereMonth('tanggal_keluar', $this->month)
            ->whereYear('tanggal_keluar', $this->year)
            ->sum('jumlah');

        $data->push($totalRow);

        return $data;
    }

    private function getKeterangan($date)
    {
        $keterangan = Expense::whereDate('tanggal_keluar', $date)
            ->whereNotNull('keterangan')
            ->pluck('keterangan')
            ->filter()
            ->unique()
            ->implode(', ');
        
        return $keterangan ?: '-';
    }

    public function headings(): array
    {
        $headings = ['NO', 'TANGGAL', 'KETERANGAN'];
        
        foreach ($this->kategoriList as $nama => $kode) {
            // Format kolom header: "202 - BEBAN GAJI"
            $headings[] = $kode . ' - ' . $nama;
        }
        
        $headings[] = 'TOTAL';
        
        return $headings;
    }

    public function title(): string
    {
        $monthName = Carbon::createFromDate($this->year, $this->month, 1)->locale('id')->translatedFormat('F');
        return 'Pengeluaran ' . $monthName . ' ' . $this->year;
    }

    public function styles(Worksheet $sheet)
    {
        $lastColumn = 'R'; // Sesuaikan dengan jumlah kolom
        $lastRow = $sheet->getHighestRow();

        return [
            // Header row
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                $lastColumn = $sheet->getHighestColumn();

                // Insert header rows
                $sheet->insertNewRowBefore(1, 4);

                // Title
                $monthName = Carbon::createFromDate($this->year, $this->month, 1)->locale('id')->translatedFormat('F');
                
                $sheet->setCellValue('A1', 'LAPORAN PENGELUARAN HARIAN');
                $sheet->setCellValue('A2', 'PT. JERNIH MULTI KOMUNIKASI');
                $sheet->setCellValue('A3', 'Periode ' . $monthName . ' ' . $this->year);
                
                // Merge cells for title
                $sheet->mergeCells('A1:' . $lastColumn . '1');
                $sheet->mergeCells('A2:' . $lastColumn . '2');
                $sheet->mergeCells('A3:' . $lastColumn . '3');

                // Style title
                $sheet->getStyle('A1:A3')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);

                // Border untuk data
                $sheet->getStyle('A5:' . $lastColumn . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);

                // Format angka
                for ($col = 'D'; $col <= $lastColumn; $col++) {
                    $sheet->getStyle($col . '6:' . $col . $lastRow)
                        ->getNumberFormat()
                        ->setFormatCode('#,##0');
                }

                // Style untuk baris total
                $sheet->getStyle('A' . $lastRow . ':' . $lastColumn . $lastRow)->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E2EFDA']
                    ],
                ]);
            },
        ];
    }
}
