<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
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

class PelangganDateSheetExport implements FromCollection, WithHeadings, WithTitle, WithStyles, WithEvents, ShouldAutoSize
{
    protected $date;
    protected $items;
    protected $totalSampaiTanggal;

    /**
     * @param string     $date               Tanggal sheet (Y-m-d)
     * @param Collection $items              Pelanggan yang created_at di tanggal ini
     * @param int        $totalSampaiTanggal Total kumulatif pelanggan dari awal hingga tanggal ini
     */
    public function __construct(string $date, Collection $items, int $totalSampaiTanggal)
    {
        $this->date = $date;
        $this->items = $items;
        $this->totalSampaiTanggal = $totalSampaiTanggal;
    }

    public function collection()
    {
        $data = collect();
        $no = 1;

        foreach ($this->items as $pelanggan) {
            // Format alamat lengkap
            $alamat = $pelanggan->alamat_jalan ?? '-';
            if ($pelanggan->rt || $pelanggan->rw) {
                $alamat .= ', RT ' . ($pelanggan->rt ?? '-') . '/RW ' . ($pelanggan->rw ?? '-');
            }
            if ($pelanggan->kecamatan) {
                $alamat .= ', ' . $pelanggan->kecamatan;
            }
            if ($pelanggan->kabupaten) {
                $alamat .= ', ' . $pelanggan->kabupaten;
            }

            $data->push([
                'no' => $no++,
                'nomer_id' => $pelanggan->nomer_id ?? '-',
                'nama_lengkap' => $pelanggan->nama_lengkap ?? '-',
                'no_whatsapp' => $pelanggan->no_whatsapp ?? '-',
                'alamat' => $alamat,
                'kecepatan' => $pelanggan->paket->kecepatan ?? '-',
                'harga' => (float) ($pelanggan->paket->harga ?? 0),
                'status' => ucfirst($pelanggan->status ?? '-'),
            ]);
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'NO',
            'ID PELANGGAN',
            'NAMA LENGKAP',
            'NO. WHATSAPP',
            'ALAMAT',
            'KECEPATAN',
            'BIAYA LANGGANAN',
            'STATUS',
        ];
    }

    public function title(): string
    {
        $date = Carbon::parse($this->date);
        $title = $date->format('d M Y');
        // Remove invalid characters for excel sheet name
        $title = str_replace(['*', ':', '/', '\\', '?', '[', ']'], '', $title);
        // Max length is 31 characters
        return substr($title, 0, 31);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '18181b']
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                $lastColumn = $sheet->getHighestColumn();
                $jumlahHariIni = $this->items->count();
                $dateFormatted = Carbon::parse($this->date)->locale('id')->translatedFormat('d F Y');

                // Insert 4 header rows at top
                $sheet->insertNewRowBefore(1, 4);

                // Title rows
                $sheet->setCellValue('A1', 'DATA PELANGGAN - ' . strtoupper($dateFormatted));
                $sheet->setCellValue('A2', 'PT. JERNIH MULTI KOMUNIKASI');
                $sheet->setCellValue('A3', 'Tanggal: ' . $dateFormatted);

                // Merge title cells
                $sheet->mergeCells('A1:' . $lastColumn . '1');
                $sheet->mergeCells('A2:' . $lastColumn . '2');
                $sheet->mergeCells('A3:' . $lastColumn . '3');

                // Style title rows
                $sheet->getStyle('A1:A3')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 13],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Border for data area (row 5 = heading, row 6+ = data)
                $sheet->getStyle('A5:' . $lastColumn . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);

                // Format harga column (G) as number
                $sheet->getStyle('G6:G' . $lastRow)
                    ->getNumberFormat()
                    ->setFormatCode('#,##0');

                // === SUMMARY SECTION ===
                $summaryStart = $lastRow + 2;

                // Summary title
                $sheet->mergeCells("E{$summaryStart}:G{$summaryStart}");
                $sheet->setCellValue("E{$summaryStart}", 'RINGKASAN');
                $sheet->getStyle("E{$summaryStart}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '18181b'],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Jumlah Hari Ini
                $row1 = $summaryStart + 1;
                $sheet->setCellValue("E{$row1}", 'Jumlah Pelanggan Hari Ini');
                $sheet->setCellValue("G{$row1}", $jumlahHariIni);
                $sheet->getStyle("E{$row1}:G{$row1}")->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ]);

                // Total Kumulatif
                $row2 = $summaryStart + 2;
                $sheet->setCellValue("E{$row2}", 'Total Pelanggan s/d ' . $dateFormatted);
                $sheet->setCellValue("G{$row2}", $this->totalSampaiTanggal);
                $sheet->getStyle("E{$row2}:G{$row2}")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E2EFDA'],
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ]);
            },
        ];
    }
}
