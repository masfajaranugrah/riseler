<?php

namespace App\Exports;

use App\Models\KasRegistrasi;
use App\Models\Tagihan;
use App\Models\Expense;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Collection;

class LaporanHarianExport implements WithMultipleSheets
{
    protected int $month;
    protected int $year;

    public function __construct(int $month, int $year)
    {
        $this->month = $month;
        $this->year  = $year;
    }

    public function sheets(): array
    {
        $sheets = [];

        $daysInMonth = Carbon::createFromDate($this->year, $this->month, 1)->daysInMonth;
        // Bulan tagihan = 1 bulan ke belakang (filter April → tagihan Maret)
        $billingDate = Carbon::createFromDate($this->year, $this->month, 1)->subMonth(1);
        $prevMonth   = $billingDate->month;
        $prevYear    = $billingDate->year;
        $startOfMonth = Carbon::createFromDate($this->year, $this->month, 1)->startOfDay();

        // ═══════════════════════════════════════════════════════════════
        // SALDO AWAL TANGGAL 1 = 0 (nol)
        // Saldo hanya berjalan dalam bulan yang di-export.
        // Setiap bulan mulai dari nol, kemudian bertambah/berkurang per hari.
        // ═══════════════════════════════════════════════════════════════
        // Running saldo: mulai dari 0, diperbarui setiap hari
        $bwRunning   = 0.0;
        $regRunning  = 0.0;
        $bankRunning = 0.0;

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::createFromDate($this->year, $this->month, $day);

            // ── Data harian ──────────────────────────────────────────────
            $cashPayments   = $this->getCashPayments($date, $prevMonth, $prevYear);
            $allExpenses    = $this->getExpenses($date);
            $kasReg         = $this->getKasRegistrasi($date);
            $bankPayments   = $this->getBankPayments($date, $prevMonth, $prevYear);

            // Pisahkan pengeluaran cash (Kas Bandwidth) & transfer (Bank JMK)
            $cashExpenses = $allExpenses->filter(function($e) {
                $tipe = strtolower($e->tipe_pembayaran ?? '');
                return empty($tipe) || str_contains($tipe, 'cash') || str_contains($tipe, 'tunai');
            });

            $bankExpenses = $allExpenses->filter(function($e) {
                $tipe = strtolower($e->tipe_pembayaran ?? '');
                return !empty($tipe) && !str_contains($tipe, 'cash') && !str_contains($tipe, 'tunai');
            });

            // ── Jumlah per seksi ─────────────────────────────────────────
            $bwPemasukan    = $cashPayments->sum('jumlah');
            $bwPengeluaran  = $cashExpenses->sum('jumlah');
            $regPemasukan   = $kasReg->sum('pemasukan');
            $regPengeluaran = $kasReg->sum('pengeluaran');
            $bankPemasukan  = $bankPayments->sum('jumlah');
            $bankPengeluaran= $bankExpenses->sum('jumlah');

            // ── Saldo akhir hari ini ─────────────────────────────────────
            // Saldo Akhir = Saldo Awal (hari ini) + Pemasukan - Pengeluaran
            $bwSaldoAkhir   = $bwRunning   + $bwPemasukan   - $bwPengeluaran;
            $regSaldoAkhir  = $regRunning  + $regPemasukan  - $regPengeluaran;
            $bankSaldoAkhir = $bankRunning + $bankPemasukan - $bankPengeluaran;

            // ── Buat sheet hanya jika ada data ───────────────────────────
            if (
                $cashPayments->isNotEmpty()  ||
                $cashExpenses->isNotEmpty()  ||
                $bankExpenses->isNotEmpty()  ||
                $kasReg->isNotEmpty()         ||
                $bankPayments->isNotEmpty()
            ) {
                $sheets[] = new LaporanHarianSheetExport(
                    date:           $date,
                    cashPayments:   $cashPayments,
                    expenses:       $cashExpenses, // Kas Bandwidth expenses
                    kasReg:         $kasReg,
                    bankPayments:   $bankPayments,
                    bankExpenses:   $bankExpenses, // Bank JMK expenses
                    // Saldo AWAL hari ini (= saldo akhir hari sebelumnya)
                    bwSaldoAwal:    $bwRunning,
                    regSaldoAwal:   $regRunning,
                    bankSaldoAwal:  $bankRunning,
                    // Saldo AKHIR hari ini
                    bwSaldoAkhir:   $bwSaldoAkhir,
                    regSaldoAkhir:  $regSaldoAkhir,
                    bankSaldoAkhir: $bankSaldoAkhir,
                );
            }

            // ── Carry forward → menjadi saldo AWAL hari berikutnya ───────
            $bwRunning   = $bwSaldoAkhir;
            $regRunning  = $regSaldoAkhir;
            $bankRunning = $bankSaldoAkhir;
        }

        return $sheets;
    }

    // ═══════════════════════════════════════════════════════════════════════
    // DATA HARIAN
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * Pembayaran CASH pada tanggal ini.
     * Termasuk:
     *  - Tagihan bulan ini yang dibayar hari ini (cash)
     *  - Outstanding bulan SEBELUMNYA yang baru dibayar hari ini (cash)
     * TIDAK termasuk:
     *  - Outstanding dari 2 bulan ke belakang atau lebih
     */
    private function getCashPayments(Carbon $date, int $prevMonth, int $prevYear): Collection
    {
        return Tagihan::with(['pelanggan', 'paket', 'rekening'])
            ->where('status_pembayaran', 'lunas')
            ->where(function ($q) {
                $q->whereNull('type_pembayaran')
                  ->orWhere('type_pembayaran', '')
                  ->orWhereHas('rekening', function ($sub) {
                      $sub->where('nama_bank', 'like', '%cash%')
                          ->orWhere('nama_bank', 'like', '%tunai%');
                  });
            })
            ->whereDate('tanggal_pembayaran', $date)
            ->where(function ($q) use ($date, $prevMonth, $prevYear) {
                // Tagihan bulan ini
                $q->where(function ($sub) use ($date) {
                    $sub->whereMonth('tanggal_mulai', $date->month)
                        ->whereYear('tanggal_mulai', $date->year);
                })
                // ATAU tagihan bulan tagihan utama (misal: Maret) saja, BUKAN semua outstanding lama
                ->orWhere(function ($sub) use ($prevMonth, $prevYear) {
                    $sub->whereMonth('tanggal_mulai', $prevMonth)
                        ->whereYear('tanggal_mulai', $prevYear);
                });
            })
            ->get()
            ->map(function ($t) {
                $t->jumlah = (float)($t->harga ?? ($t->paket->harga ?? 0));
                return $t;
            });
    }

    /**
     * Pembayaran via BANK pada tanggal ini.
     * Logika sama dengan cash tapi type_pembayaran IS NOT NULL.
     */
    private function getBankPayments(Carbon $date, int $prevMonth, int $prevYear): Collection
    {
        return Tagihan::with(['pelanggan', 'paket', 'rekening'])
            ->where('status_pembayaran', 'lunas')
            ->whereNotNull('type_pembayaran')
            ->where('type_pembayaran', '!=', '')
            ->whereHas('rekening', function ($sub) {
                $sub->where('nama_bank', 'not like', '%cash%')
                    ->where('nama_bank', 'not like', '%tunai%');
            })
            ->whereDate('tanggal_pembayaran', $date)
            ->where(function ($q) use ($date, $prevMonth, $prevYear) {
                $q->where(function ($sub) use ($date) {
                    $sub->whereMonth('tanggal_mulai', $date->month)
                        ->whereYear('tanggal_mulai', $date->year);
                })
                ->orWhere(function ($sub) use ($prevMonth, $prevYear) {
                    $sub->whereMonth('tanggal_mulai', $prevMonth)
                        ->whereYear('tanggal_mulai', $prevYear);
                });
            })
            ->get()
            ->map(function ($t) {
                $t->jumlah = (float)($t->harga ?? ($t->paket->harga ?? 0));
                return $t;
            });
    }

    /** Pengeluaran (Expenses) pada tanggal ini */
    private function getExpenses(Carbon $date): Collection
    {
        return Expense::whereDate('tanggal_keluar', $date)->get();
    }

    /** Kas Registrasi pada tanggal ini */
    private function getKasRegistrasi(Carbon $date): Collection
    {
        return KasRegistrasi::whereDate('tanggal', $date)->get();
    }
}
