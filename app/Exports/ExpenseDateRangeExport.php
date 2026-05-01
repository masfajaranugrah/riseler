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

class ExpenseDateRangeExport implements FromCollection, WithHeadings, WithTitle, WithStyles, WithEvents, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = Carbon::parse($startDate);
        $this->endDate = Carbon::parse($endDate);
    }

    public function collection()
    {
        $expenses = Expense::whereBetween('tanggal_keluar', [
            $this->startDate->startOfDay(),
            $this->endDate->endOfDay()
        ])
        ->orderBy('tanggal_keluar', 'asc')
        ->get();

        $data = collect();
        $no = 1;

        foreach ($expenses as $expense) {
            $data->push([
                'no' => $no++,
                'tanggal' => Carbon::parse($expense->tanggal_keluar)->format('d-m-Y'),
                'jam' => Carbon::parse($expense->tanggal_keluar)->format('H:i'),
                'kode' => $expense->kode,
                'kategori' => $expense->kategori,
                'keterangan' => $expense->keterangan ?? '-',
                'jumlah' => $expense->jumlah,
            ]);
        }

        // Total row
        $data->push([
            'no' => '',
            'tanggal' => '',
            'jam' => '',
            'kode' => '',
            'kategori' => '',
            'keterangan' => 'TOTAL',
            'jumlah' => $expenses->sum('jumlah'),
        ]);

        return $data;
    }

    public function headings(): array
    {
        return [
            'NO',
            'TANGGAL',
            'JAM',
            'KODE',
            'KATEGORI',
            'KETERANGAN',
            'JUMLAH (Rp)',
        ];
    }

    public function title(): string
    {
        return 'Detail Pengeluaran';
    }

    public function styles(Worksheet $sheet)
    {
        return [
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
                $sheet->setCellValue('A1', 'LAPORAN DETAIL PENGELUARAN');
                $sheet->setCellValue('A2', 'PT. JERNIH MULTI KOMUNIKASI');
                $sheet->setCellValue('A3', 'Periode: ' . $this->startDate->format('d M Y') . ' - ' . $this->endDate->format('d M Y'));
                
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

                // Format angka untuk kolom jumlah (G)
                $sheet->getStyle('G6:G' . $lastRow)
                    ->getNumberFormat()
                    ->setFormatCode('#,##0');

                // Bold total row
                $sheet->getStyle('A' . $lastRow . ':G' . $lastRow)->applyFromArray([
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
