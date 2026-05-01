<?php

namespace App\Exports;

use App\Models\LaporanKabel;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class LaporanKabelExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithEvents, WithTitle
{
    protected ?string $date;
    protected ?int    $month;
    protected ?int    $year;
    protected ?string $wilayah;
    protected string  $search;
    protected string  $periodeLabel;
    protected string  $sheetTitle;
    protected int     $no = 0;

    public function __construct(
        ?string $date       = null,
        ?int    $month      = null,
        ?int    $year       = null,
        ?string $wilayah    = null,
        string  $search     = '',
        string  $sheetTitle = 'Laporan Kabel'
    ) {
        $this->date       = $date;
        $this->month      = $month;
        $this->year       = $year;
        $this->wilayah    = $wilayah;
        $this->search     = $search;
        $this->sheetTitle = $sheetTitle;

        // Bangun label periode untuk baris info di Excel
        if (filled($date)) {
            $this->periodeLabel = 'Tanggal: ' . Carbon::parse($date)->translatedFormat('d F Y');
        } elseif (filled($month) || filled($year)) {
            $parts = [];
            if (filled($month)) {
                $parts[] = Carbon::create()->month($month)->translatedFormat('F');
            }
            if (filled($year)) {
                $parts[] = $year;
            }
            $this->periodeLabel = 'Periode: ' . implode(' ', $parts);
        } else {
            $this->periodeLabel = 'Semua Data';
        }
    }

    public function title(): string
    {
        return $this->sheetTitle;
    }

    public function query()
    {
        $query = LaporanKabel::with('employee:id,full_name')
            ->select([
                'id', 'nama_pelanggan', 'employee_id', 'wilayah',
                'alamat', 'tarikan_meter', 'jenis_kabel', 'sisi_core',
                'keterangan', 'created_at',
            ]);

        // Filter tanggal
        if (filled($this->date)) {
            $query->whereDate('created_at', Carbon::parse($this->date)->toDateString());
        } else {
            if (filled($this->month)) {
                $query->whereMonth('created_at', $this->month);
            }
            if (filled($this->year)) {
                $query->whereYear('created_at', $this->year);
            }
        }

        if (filled($this->wilayah)) {
            $query->where('wilayah', $this->wilayah);
        }

        if ($this->search !== '') {
            $s = $this->search;
            $query->where(function ($q) use ($s) {
                $q->where('nama_pelanggan', 'like', "%{$s}%")
                  ->orWhere('alamat', 'like', "%{$s}%")
                  ->orWhere('wilayah', 'like', "%{$s}%")
                  ->orWhere('jenis_kabel', 'like', "%{$s}%")
                  ->orWhere('keterangan', 'like', "%{$s}%")
                  ->orWhereHas('employee', function ($eq) use ($s) {
                      $eq->where('full_name', 'like', "%{$s}%");
                  });
            });
        }

        return $query->latest();
    }

    public function headings(): array
    {
        return [
            ['LAPORAN KABEL - ' . strtoupper($this->periodeLabel)], // Baris 1: Judul
            [
                'NO',
                'NAMA PELANGGAN',
                'NAMA TEKNISI',
                'WILAYAH',
                'ALAMAT',
                'TARIKAN (METER)',
                'JENIS KABEL',
                'SISA KABEL (METER)',
                'KETERANGAN',
                'TANGGAL INPUT',
            ] // Baris 2: Header kolom
        ];
    }

    public function map($item): array
    {
        $this->no++;

        return [
            $this->no,
            $item->nama_pelanggan,
            optional($item->employee)->full_name ?: '-',
            $item->wilayah ?: '-',
            $item->alamat,
            (float) $item->tarikan_meter,
            strtoupper((string) $item->jenis_kabel),
            (float) $item->sisi_core,
            $item->keterangan ?: '-',
            optional($item->created_at)->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // Format angka meter di kolom F (tarikan) dan H (sisa kabel)
        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle("F3:F{$highestRow}")->getNumberFormat()->setFormatCode('0.00');
        $sheet->getStyle("H3:H{$highestRow}")->getNumberFormat()->setFormatCode('0.00');

        return [
            // Style untuk Judul Sheet di baris 1
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 14,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
            ],
            // Style untuk Header Kolom di baris 2
            2 => [
                'font' => [
                    'bold' => true,
                    'size' => 11,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '111827'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => '000000'],
                    ],
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        $periodeLabel = $this->periodeLabel;
        $wilayah      = $this->wilayah;

        return [
            AfterSheet::class => function (AfterSheet $event) use ($periodeLabel, $wilayah) {
                $sheet       = $event->sheet->getDelegate();
                $lastDataRow = $sheet->getHighestRow();
                $startRow    = $lastDataRow + 2;

                // Merge kolom untuk Judul di Baris 1
                $sheet->mergeCells("A1:J1");

                // Baris ringkasan total tarikan
                $sheet->setCellValue("A{$startRow}", 'RINGKASAN');
                $sheet->mergeCells("A{$startRow}:J{$startRow}");
                $sheet->getStyle("A{$startRow}")->getFont()->setBold(true);

                $row = $startRow + 1;
                $sheet->setCellValue("A{$row}", 'Periode');
                $sheet->setCellValue("B{$row}", $periodeLabel);

                $row++;
                $sheet->setCellValue("A{$row}", 'Wilayah');
                $sheet->setCellValue("B{$row}", $wilayah ?: 'Semua Wilayah');

                $row++;
                // Hitung total tarikan (kolom F = kolom 6)
                // Baris data mulai dari baris ke-3
                $totalTarikan = 0;
                for ($r = 3; $r <= $lastDataRow; $r++) {
                    $val = $sheet->getCell("F{$r}")->getValue();
                    $totalTarikan += is_numeric($val) ? (float) $val : 0;
                }
                $sheet->setCellValue("A{$row}", 'Total Tarikan Kabel');
                $sheet->setCellValue("B{$row}", number_format($totalTarikan, 2, '.', '') . ' M');
                $sheet->getStyle("A{$row}:B{$row}")->getFont()->setBold(true);

                $row++;
                $sheet->setCellValue("A{$row}", 'Total Data');
                $sheet->setCellValue("B{$row}", ($lastDataRow - 2 < 0 ? 0 : $lastDataRow - 2) . ' record');
                $sheet->getStyle("A{$row}:B{$row}")->getFont()->setBold(true);

                // Border ringkasan
                $sheet->getStyle("A{$startRow}:B{$row}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

                $sheet->getStyle("A{$startRow}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('E5E7EB');

                // Dicetak pada
                $row += 2;
                $sheet->setCellValue("A{$row}", 'Dicetak: ' . now()->format('d/m/Y H:i'));
                $sheet->getStyle("A{$row}")->getFont()->setItalic(true)->setSize(9);
            },
        ];
    }
}
