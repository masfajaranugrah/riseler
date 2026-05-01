<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LaporanHarianSheetExport implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    protected Carbon     $date;
    protected Collection $cashPayments;
    protected Collection $expenses;
    protected Collection $kasReg;
    protected Collection $bankPayments;
    protected Collection $bankExpenses;
    protected float $bwSaldoAwal;
    protected float $regSaldoAwal;
    protected float $bankSaldoAwal;
    protected float $bwSaldoAkhir;
    protected float $regSaldoAkhir;
    protected float $bankSaldoAkhir;

    // Track row positions for styling
    protected array $styleMap = [];
    protected int $lastRow = 1;

    public function __construct(
        Carbon $date,
        Collection $cashPayments,
        Collection $expenses,
        Collection $kasReg,
        Collection $bankPayments,
        Collection $bankExpenses,
        float $bwSaldoAwal,
        float $regSaldoAwal,
        float $bankSaldoAwal,
        float $bwSaldoAkhir,
        float $regSaldoAkhir,
        float $bankSaldoAkhir
    ) {
        $this->date          = $date;
        $this->cashPayments  = $cashPayments;
        $this->expenses      = $expenses; // This is cashExpenses
        $this->kasReg        = $kasReg;
        $this->bankPayments  = $bankPayments;
        $this->bankExpenses  = $bankExpenses;
        $this->bwSaldoAwal   = $bwSaldoAwal;
        $this->regSaldoAwal  = $regSaldoAwal;
        $this->bankSaldoAwal = $bankSaldoAwal;
        $this->bwSaldoAkhir  = $bwSaldoAkhir;
        $this->regSaldoAkhir = $regSaldoAkhir;
        $this->bankSaldoAkhir = $bankSaldoAkhir;
    }

    public function title(): string
    {
        return $this->date->format('d');
    }

    public function array(): array
    {
        $rows = [];
        $r = 1;

        $dayName = strtoupper($this->toIndonesianDay($this->date->dayOfWeek));
        $monthName = strtoupper($this->toIndonesianMonth($this->date->month));
        
        // Bulan tagihan = 1 bulan ke belakang (filter April → Maret)
        $billingMonthDate = $this->date->copy()->subMonths(1);
        $billingMonthName = strtoupper($this->toIndonesianMonth($billingMonthDate->month));

        // ───── TITLE ─────────────────────────────────────────────────────────
        $rows[] = ['LAPORAN HARIAN', '', '', '', '', ''];
        $this->styleMap['title_row'] = $r++;

        $rows[] = [$dayName . ', ' . $this->date->format('d') . ' ' . $monthName . ' ' . $this->date->year, '', '', '', '', ''];
        $this->styleMap['subtitle_row'] = $r++;

        $rows[] = ['PEMASUKAN ' . $billingMonthName . ' - PENGELUARAN ' . $monthName, '', '', '', '', ''];
        $this->styleMap['subsubtitle_row'] = $r++;

        $rows[] = ['', '', '', '', '', ''];
        $r++;  // blank

        // ════════════════════════════════════════════════════════════════
        // SECTION 1: KAS BANDWIDTH
        // ════════════════════════════════════════════════════════════════
        $rows[] = ['KAS BANDWIDTH', '', '', '', '', ''];
        $this->styleMap['bw_header'] = $r++;

        // Table header
        $rows[] = ['NO', 'KETERANGAN', 'PEMASUKAN', 'PENGELUARAN', 'SALDO', 'Ket'];
        $this->styleMap['bw_thead'] = $r++;

        // Saldo Awal row
        $rows[] = [1, 'Saldo Awal', '', '', $this->bwSaldoAwal, ''];
        $this->styleMap['bw_saldo_awal'] = $r++;

        $no = 2;
        $bwPemasukan  = 0;
        $bwPengeluaran = 0;
        $this->styleMap['bw_data_start'] = $r;

        // Pengeluaran (expenses) rows
        foreach ($this->expenses as $exp) {
            $metode = strtoupper($exp->tipe_pembayaran ?? 'CASH');
            if ($metode === 'CASH') $metode = 'Cash / Tunai';
            $rows[] = [$no++, $exp->keterangan ?? '-', '', $exp->jumlah, '', $metode];
            $r++;
            $bwPengeluaran += $exp->jumlah;
        }

        // Pemasukan (cash payments) rows
        foreach ($this->cashPayments as $pay) {
            $name = 'Pembayaran ' . ucfirst(strtolower($billingMonthName)) . ' - ' . ($pay->pelanggan->nama_lengkap ?? '-');
            $rows[] = [$no++, $name, $pay->jumlah, '', '', 'Cash / Tunai'];
            $r++;
            $bwPemasukan += $pay->jumlah;
        }

        $this->styleMap['bw_data_end'] = $r - 1;

        // Blank if needed, but per the request, we just continue. Or add blank row.
        // Let's not add too many blank rows between data and totals to match accounting looks.
        
        // Jumlah row
        $rows[] = ['', 'Jumlah', $bwPemasukan, $bwPengeluaran, '', ''];
        $this->styleMap['bw_jumlah'] = $r++;

        // Saldo Akhir row
        $rows[] = ['', 'Saldo Akhir', '', '', $this->bwSaldoAkhir, ''];
        $this->styleMap['bw_saldo_akhir'] = $r++;

        $rows[] = ['', '', '', '', '', ''];
        $r++;  // blank

        // ════════════════════════════════════════════════════════════════
        // SECTION 2: KAS REGISTRASI
        // ════════════════════════════════════════════════════════════════
        $rows[] = ['KAS REGISTRASI', '', '', '', '', ''];
        $this->styleMap['reg_header'] = $r++;

        $rows[] = ['NO', 'KETERANGAN', 'PEMASUKAN', 'PENGELUARAN', 'SALDO', ''];
        $this->styleMap['reg_thead'] = $r++;

        $rows[] = [1, 'Saldo Awal', '', '', $this->regSaldoAwal, ''];
        $this->styleMap['reg_saldo_awal'] = $r++;

        $no = 2;
        $regPemasukan  = 0;
        $regPengeluaran = 0;
        $this->styleMap['reg_data_start'] = $r;

        foreach ($this->kasReg as $kr) {
            if ($kr->pengeluaran > 0) {
                $rows[] = [$no++, $kr->keterangan, '', $kr->pengeluaran, '', ''];
                $regPengeluaran += $kr->pengeluaran;
            } else {
                $rows[] = [$no++, $kr->keterangan, $kr->pemasukan, '', '', ''];
                $regPemasukan += $kr->pemasukan;
            }
            $r++;
        }

        $this->styleMap['reg_data_end'] = $r - 1;

        // Jumlah
        $rows[] = ['', 'Jumlah', $regPemasukan, $regPengeluaran, '', ''];
        $this->styleMap['reg_jumlah'] = $r++;

        // Saldo Akhir
        $rows[] = ['', 'Saldo Akhir', '', '', $this->regSaldoAkhir, ''];
        $this->styleMap['reg_saldo_akhir'] = $r++;

        $rows[] = ['', '', '', '', '', ''];
        $r++;

        // ════════════════════════════════════════════════════════════════
        // SECTION 3: REKENING BANK JMK
        // ════════════════════════════════════════════════════════════════
        $rows[] = ['REKENING BANK JMK', '', '', '', '', ''];
        $this->styleMap['bank_header'] = $r++;

        $rows[] = ['NO', 'KETERANGAN', 'PEMASUKAN', 'PENGELUARAN', 'SALDO', 'Ket'];
        $this->styleMap['bank_thead'] = $r++;

        $rows[] = [1, 'Saldo Awal', '', '', $this->bankSaldoAwal, ''];
        $this->styleMap['bank_saldo_awal'] = $r++;

        $no = 2;
        $bankPemasukan = 0;
        $bankPengeluaran = 0;
        $this->styleMap['bank_data_start'] = $r;

        // Pemasukan dari $bankPayments
        foreach ($this->bankPayments as $pay) {
            $name     = 'Pembayaran ' . ucfirst(strtolower($billingMonthName)) . ' - ' . ($pay->pelanggan->nama_lengkap ?? '-');
            $bankCode = $this->getBankCode($pay->rekening->nama_bank ?? '');
            $rows[] = [$no++, $name, $pay->jumlah, '', '', $bankCode];
            $r++;
            $bankPemasukan += $pay->jumlah;
        }

        // Pengeluaran dari $bankExpenses
        foreach ($this->bankExpenses as $exp) {
            $name     = $exp->keterangan ?? $exp->kategori;
            $bankCode = $this->getBankCode($exp->tipe_pembayaran ?? '');
            $rows[] = [$no++, $name, '', $exp->jumlah, '', $bankCode];
            $r++;
            $bankPengeluaran += $exp->jumlah;
        }

        $this->styleMap['bank_data_end'] = $r - 1;

        $rows[] = ['', 'Jumlah', $bankPemasukan, $bankPengeluaran, '', ''];
        $this->styleMap['bank_jumlah'] = $r++;

        $rows[] = ['', 'Saldo Akhir', '', '', $this->bankSaldoAkhir, ''];
        $this->styleMap['bank_saldo_akhir'] = $r++;

        $rows[] = ['', '', '', '', '', ''];
        $r++;

        // ════════════════════════════════════════════════════════════════
        // SECTION 4: LAPORAN HARIAN SUMMARY
        // ════════════════════════════════════════════════════════════════
        $rows[] = ['', 'LAPORAN HARIAN ' . $this->date->format('d') . ' ' . $monthName . ' ' . $this->date->year, '', '', '', ''];
        $this->styleMap['sum_title'] = $r++;

        $rows[] = ['Tanggal', 'Keterangan', 'Saldo Awal', 'Pemasukan', 'Pengeluaran', 'Saldo Akhir'];
        $this->styleMap['sum_thead'] = $r++;

        $dateDisplay = $this->date->format('d/m/Y');
        $rows[] = [$dateDisplay, 'KAS BANDWIDTH', $this->bwSaldoAwal, $bwPemasukan, $bwPengeluaran, $this->bwSaldoAkhir];
        $this->styleMap['sum_row1'] = $r++;

        $rows[] = ['', 'KAS REGISTRASI', $this->regSaldoAwal, $regPemasukan, $regPengeluaran, $this->regSaldoAkhir];
        $this->styleMap['sum_row2'] = $r++;

        $rows[] = ['', 'REKENING BANK', $this->bankSaldoAwal, $bankPemasukan, $bankPengeluaran, $this->bankSaldoAkhir];
        $this->styleMap['sum_row3'] = $r++;

        $totalPemasukan   = $bwPemasukan + $regPemasukan + $bankPemasukan;
        $totalPengeluaran = $bwPengeluaran + $regPengeluaran + $bankPengeluaran;
        $rows[] = ['', 'TOTAL', '', $totalPemasukan, $totalPengeluaran, ''];
        $this->styleMap['sum_total'] = $r++;

        $this->lastRow = $r;

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        $this->applyStyles($sheet);
        return [];
    }

    private function applyStyles(Worksheet $sheet): void
    {
        // Custom format: Rp dengan koma pemisah ribuan, angka 0 tetap tampil sebagai Rp 0
        $currencyFormat = '"Rp "#,##0;-"Rp "#,##0;"Rp "0';

        // Helper: apply border to range
        $border = function(string $range) use ($sheet) {
            $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        };
        $bold = function(string $range) use ($sheet) {
            $sheet->getStyle($range)->getFont()->setBold(true);
        };
        $center = function(string $range) use ($sheet) {
            $sheet->getStyle($range)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        };
        $right = function(string $range) use ($sheet) {
            $sheet->getStyle($range)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        };
        $bgDark = function(string $range) use ($sheet) {
            $sheet->getStyle($range)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('1C1C1C');
            $sheet->getStyle($range)->getFont()->getColor()->setRGB('FFFFFF');
        };
        $bgGray = function(string $range) use ($sheet) {
            $sheet->getStyle($range)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F0F0F0');
        };

        // ─── TITLE ─────────────────────────────────────────────────────
        if (isset($this->styleMap['title_row'])) {
            $tr = $this->styleMap['title_row'];
            $sheet->mergeCells("A{$tr}:F{$tr}");
            $center("A{$tr}");
            $sheet->getStyle("A{$tr}")->getFont()->setBold(true)->setSize(14);
        }
        if (isset($this->styleMap['subtitle_row'])) {
            $sr = $this->styleMap['subtitle_row'];
            $sheet->mergeCells("A{$sr}:F{$sr}");
            $center("A{$sr}");
            $sheet->getStyle("A{$sr}")->getFont()->setBold(true)->setSize(11);
        }
        if (isset($this->styleMap['subsubtitle_row'])) {
            $ssr = $this->styleMap['subsubtitle_row'];
            $sheet->mergeCells("A{$ssr}:F{$ssr}");
            $center("A{$ssr}");
            $sheet->getStyle("A{$ssr}")->getFont()->setSize(10);
        }

        // ─── BW, REG, BANK SECTION ─────────────────────────────────────
        // Section titles
        foreach (['bw_header', 'reg_header', 'bank_header'] as $key) {
            if (isset($this->styleMap[$key])) {
                $row = $this->styleMap[$key];
                $bold("A{$row}");
                $sheet->getStyle("A{$row}")->getFont()->setSize(11);
            }
        }

        // Table headers
        foreach (['bw_thead', 'reg_thead', 'bank_thead'] as $key) {
            if (isset($this->styleMap[$key])) {
                $row = $this->styleMap[$key];
                $bgDark("A{$row}:F{$row}");
                $bold("A{$row}:F{$row}");
                $center("A{$row}:F{$row}");
                $border("A{$row}:F{$row}");
            }
        }

        // Saldo Awal rows
        foreach (['bw_saldo_awal', 'reg_saldo_awal', 'bank_saldo_awal'] as $key) {
            if (isset($this->styleMap[$key])) {
                $row = $this->styleMap[$key];
                $border("A{$row}:F{$row}");
                $bgGray("A{$row}:F{$row}");
                $bold("A{$row}:F{$row}");
                $sheet->getStyle("E{$row}")->getNumberFormat()->setFormatCode($currencyFormat);
            }
        }

        // Jumlah rows
        foreach (['bw_jumlah', 'reg_jumlah', 'bank_jumlah'] as $key) {
            if (isset($this->styleMap[$key])) {
                $row = $this->styleMap[$key];
                $border("A{$row}:F{$row}");
                $bgGray("A{$row}:F{$row}");
                $bold("A{$row}:F{$row}");
                $right("B{$row}");
                $sheet->getStyle("C{$row}:D{$row}")->getNumberFormat()->setFormatCode($currencyFormat);
            }
        }

        // Saldo Akhir rows
        foreach (['bw_saldo_akhir', 'reg_saldo_akhir', 'bank_saldo_akhir'] as $key) {
            if (isset($this->styleMap[$key])) {
                $row = $this->styleMap[$key];
                $border("A{$row}:F{$row}");
                $bgDark("A{$row}:F{$row}");
                $bold("A{$row}:F{$row}");
                $right("B{$row}");
                $sheet->getStyle("E{$row}")->getNumberFormat()->setFormatCode($currencyFormat);
            }
        }

        // Data rows border + formatting
        foreach (['bw' => ['data_start', 'data_end'], 'reg' => ['data_start', 'data_end'], 'bank' => ['data_start', 'data_end']] as $prefix => $keys) {
            $startKey = "{$prefix}_data_start";
            $endKey   = "{$prefix}_data_end";
            if (isset($this->styleMap[$startKey], $this->styleMap[$endKey])) {
                $start = $this->styleMap[$startKey];
                $end   = $this->styleMap[$endKey];
                if ($end >= $start) {
                    $border("A{$start}:F{$end}");
                    $sheet->getStyle("C{$start}:E{$end}")->getNumberFormat()->setFormatCode($currencyFormat);
                }
            }
        }

        // ─── SUMMARY SECTION ───────────────────────────────────────────
        if (isset($this->styleMap['sum_title'])) {
            $row = $this->styleMap['sum_title'];
            $sheet->mergeCells("B{$row}:F{$row}");
            $center("B{$row}");
            $sheet->getStyle("B{$row}")->getFont()->setBold(true)->setSize(13);
        }
        if (isset($this->styleMap['sum_thead'])) {
            $row = $this->styleMap['sum_thead'];
            $bgDark("A{$row}:F{$row}");
            $bold("A{$row}:F{$row}");
            $center("A{$row}:F{$row}");
            $border("A{$row}:F{$row}");
        }
        foreach (['sum_row1', 'sum_row2', 'sum_row3'] as $key) {
            if (isset($this->styleMap[$key])) {
                $row = $this->styleMap[$key];
                $border("A{$row}:F{$row}");
                $sheet->getStyle("C{$row}:F{$row}")->getNumberFormat()->setFormatCode($currencyFormat);
            }
        }
        if (isset($this->styleMap['sum_total'])) {
            $row = $this->styleMap['sum_total'];
            $border("A{$row}:F{$row}");
            $bgDark("A{$row}:F{$row}");
            $bold("A{$row}:F{$row}");
            $right("B{$row}");
            $sheet->getStyle("D{$row}:E{$row}")->getNumberFormat()->setFormatCode($currencyFormat);
        }

        // General formatting
        $sheet->getStyle("A1:A{$this->lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("B1:B{$this->lastRow}")->getAlignment()->setWrapText(true);
        // Bank ket column alignment
        $sheet->getStyle("F1:F{$this->lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 45,
            'C' => 18,
            'D' => 18,
            'E' => 18,
            'F' => 12,
        ];
    }

    private function getBankCode(string $bankName): string
    {
        $name = strtolower($bankName);
        if (str_contains($name, 'bri'))     return 'BRI';
        if (str_contains($name, 'bsi'))     return 'BSI';
        if (str_contains($name, 'mandiri')) return 'M';
        if (str_contains($name, 'bni'))     return 'BNI';
        if (str_contains($name, 'jago'))    return 'J';
        if (str_contains($name, 'bca'))     return 'BCA';
        if (str_contains($name, 'flazz') || str_contains($name, 'flash')) return 'F';
        return strtoupper(substr($bankName, 0, 3));
    }

    private function toIndonesianDay(int $dayOfWeek): string
    {
        return ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'][$dayOfWeek] ?? '';
    }

    private function toIndonesianMonth(int $month): string
    {
        return ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][$month] ?? '';
    }
}
