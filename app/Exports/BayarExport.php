<?php

namespace App\Exports;

use App\Models\Tagihan;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class BayarExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithEvents
{
    protected $status;
    protected $tanggal;
    protected $bulan;
    protected $tahun;
    protected $totalsByBank = [];
    protected $totalAll = 0;

    public function __construct(
        $status = null,
        $tanggal = null,
        $bulan = null,
        $tahun = null
    )
    {
        $this->status = $status;
        $this->tanggal = $tanggal;
        $this->bulan = $bulan;
        $this->tahun = $tahun;

        $this->buildTotals();
    }

    /**
     * Query builder untuk data export
     */
    public function query()
    {
        $query = Tagihan::with(['pelanggan', 'paket', 'rekening'])
            ->leftJoin('rekenings', 'rekenings.id', '=', 'tagihans.type_pembayaran')
            ->select('tagihans.*');

        if ($this->status) {
            $query->where('status_pembayaran', $this->status);
        }

        if ($this->tanggal) {
            $query->whereDay('tanggal_pembayaran', $this->tanggal);
        }

        if ($this->bulan) {
            $query->whereMonth('tanggal_pembayaran', $this->bulan);
        }

        if ($this->tahun) {
            $query->whereYear('tanggal_pembayaran', $this->tahun);
        }

        return $query
            ->orderByRaw('COALESCE(rekenings.nama_bank, "Tanpa Bank") ASC')
            ->orderBy('tagihans.created_at', 'desc');
    }

    /**
     * Header kolom Excel
     */
    public function headings(): array
    {
        return [
            'NO',
            'NO. ID PELANGGAN',
            'NAMA LENGKAP',
            'NO. WHATSAPP',
            'NAMA PAKET',
            'HARGA PAKET',
            'KECEPATAN',
            'TANGGAL MULAI',
            'JATUH TEMPO',
            'STATUS PEMBAYARAN',
            'JENIS TAGIHAN',
            'TYPE PEMBAYARAN',
            'TANGGAL PEMBAYARAN',
            'CATATAN'
        ];
    }

    /**
     * Mapping data ke Excel
     * ?? HARGA DIKIRIM SEBAGAI ANGKA (BISA DI SUM)
     */
    public function map($tagihan): array
    {
        static $no = 0;
        $no++;

        // Tentukan bulan tagihan (prioritas tanggal_mulai -> tanggal_berakhir -> sekarang)
        $billingMonth = $tagihan->tanggal_mulai
            ? \Carbon\Carbon::parse($tagihan->tanggal_mulai)->startOfMonth()
            : ($tagihan->tanggal_berakhir
                ? \Carbon\Carbon::parse($tagihan->tanggal_berakhir)->startOfMonth()
                : now()->startOfMonth());

        $cutoff = $tagihan->tanggal_berakhir
            ? \Carbon\Carbon::parse($tagihan->tanggal_berakhir)->endOfDay()
            : $billingMonth->copy()->day(7)->endOfDay();

        $paymentDate = $tagihan->tanggal_pembayaran
            ? \Carbon\Carbon::parse($tagihan->tanggal_pembayaran)->endOfDay()
            : ($tagihan->tanggal_upload_pembayaran
                ? \Carbon\Carbon::parse($tagihan->tanggal_upload_pembayaran)->endOfDay()
                : now());

        $isOutstanding = $paymentDate->greaterThan($cutoff);
        $bulanLabel = $billingMonth->locale('id')->translatedFormat('F Y');
        $jenisTagihan = $isOutstanding
            ? 'Outstanding ' . $bulanLabel
            : 'Pembayaran ' . $bulanLabel;

        return [
            $no,
            $tagihan->pelanggan->nomer_id ?? '-',
            $tagihan->pelanggan->nama_lengkap ?? '-',
            $tagihan->pelanggan->no_whatsapp ?? '-',
            $tagihan->paket->nama_paket ?? '-',

            // ?? ANGKA MURNI (INI KUNCI SUPAYA SUM BISA)
            (float) ($tagihan->paket->harga ?? 0),

            ($tagihan->paket->kecepatan ?? '-') . ' Mbps',
            $tagihan->tanggal_mulai
                ? \Carbon\Carbon::parse($tagihan->tanggal_mulai)->format('d/m/Y')
                : '-',
            $tagihan->tanggal_berakhir
                ? \Carbon\Carbon::parse($tagihan->tanggal_berakhir)->format('d/m/Y')
                : '-',
            strtoupper($tagihan->status_pembayaran ?? 'BELUM BAYAR'),
            $jenisTagihan,
            $tagihan->rekening->nama_bank ?? '-',
            $tagihan->tanggal_pembayaran
                ? \Carbon\Carbon::parse($tagihan->tanggal_pembayaran)->format('d/m/Y H:i')
                : '-',
            $tagihan->catatan ?? '-'
        ];
    }

    /**
     * Styling Excel
     */
    public function styles(Worksheet $sheet)
    {
        // ?? FORMAT RUPIAH TANPA KOMA & TITIK
        $highestRow = $sheet->getHighestRow();

        // Kolom F = HARGA PAKET
        $sheet->getStyle("F2:F{$highestRow}")
            ->getNumberFormat()
            ->setFormatCode('"Rp "0');

        return [
            // Header row
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '696CFF'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Tambahkan ringkasan total per bank di bawah data.
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $dataLastRow = $sheet->getHighestRow();
                $startRow = $dataLastRow + 2;

                $sheet->setCellValue("A{$startRow}", 'RINGKASAN TOTAL PER BANK');
                $sheet->mergeCells("A{$startRow}:E{$startRow}");
                $sheet->getStyle("A{$startRow}")
                    ->getFont()->setBold(true);

                $row = $startRow + 1;
                foreach ($this->totalsByBank as $bank => $total) {
                    $sheet->setCellValue("A{$row}", $bank);
                    $formattedTotal = 'Rp ' . number_format((float) $total, 0, ',', '.');
                    $sheet->setCellValueExplicit("B{$row}", $formattedTotal, DataType::TYPE_STRING);
                    $row++;
                }

                // Total keseluruhan
                $sheet->setCellValue("A{$row}", 'Total Semua Bank');
                $formattedTotalAll = 'Rp ' . number_format((float) $this->totalAll, 0, ',', '.');
                $sheet->setCellValueExplicit("B{$row}", $formattedTotalAll, DataType::TYPE_STRING);
                $sheet->getStyle("A{$row}:B{$row}")->getFont()->setBold(true);

                // Border ringkasan
                $sheet->getStyle("A{$startRow}:B{$row}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);
            },
        ];
    }

    /**
     * Hitung total per bank sesuai filter export.
     */
    private function buildTotals(): void
    {
        $query = Tagihan::leftJoin('rekenings', 'rekenings.id', '=', 'tagihans.type_pembayaran')
            ->leftJoin('pakets', 'pakets.id', '=', 'tagihans.paket_id')
            ->selectRaw('COALESCE(rekenings.nama_bank, "Tanpa Bank") as bank, SUM(COALESCE(tagihans.harga, pakets.harga, 0)) as total');

        if ($this->status) {
            $query->where('tagihans.status_pembayaran', $this->status);
        }

        if ($this->tanggal) {
            $query->whereDay('tagihans.tanggal_pembayaran', $this->tanggal);
        }

        if ($this->bulan) {
            $query->whereMonth('tagihans.tanggal_pembayaran', $this->bulan);
        }

        if ($this->tahun) {
            $query->whereYear('tagihans.tanggal_pembayaran', $this->tahun);
        }

        $this->totalsByBank = $query->groupBy('bank')->pluck('total', 'bank')->toArray();
        $this->totalAll = array_sum($this->totalsByBank);
    }
}
