<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SaldoAwal extends Model
{
    use HasFactory;

    protected $table = 'saldo_awals';

    // Matikan auto increment
    public $incrementing = false;

    // Tipe primary key adalah string (UUID)
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'bulan',
        'tahun',
        'omset_dedicated',
        'omset_homenet_kotor',
        'omset_homenet_bersih',
        // Field pemasukan manual
        'pemasukan_registrasi',
        'pemasukan_dedicated_potongan',
        'pemasukan_homenet_kotor',
        'pemasukan_homenet_potongan',
        'pemasukan_homenet_bersih',
        // Field piutang manual
        'piutang_dedicated',
        'piutang_homenet',
        'piutang_bulan_sebelumnya',
        'piutang_periode_sebelumnya',
        'piutang_tahun_lalu',
    ];

    protected $casts = [
        'omset_dedicated' => 'decimal:2',
        'omset_homenet_kotor' => 'decimal:2',
        'omset_homenet_bersih' => 'decimal:2',
        'pemasukan_registrasi' => 'decimal:2',
        'pemasukan_dedicated_potongan' => 'decimal:2',
        'pemasukan_homenet_kotor' => 'decimal:2',
        'pemasukan_homenet_potongan' => 'decimal:2',
        'pemasukan_homenet_bersih' => 'decimal:2',
        'piutang_dedicated' => 'decimal:2',
        'piutang_homenet' => 'decimal:2',
        'piutang_bulan_sebelumnya' => 'decimal:2',
        'piutang_periode_sebelumnya' => 'decimal:2',
        'piutang_tahun_lalu' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        // Generate UUID sebelum create
        static::creating(function ($saldoAwal) {
            if (! $saldoAwal->id) {
                $saldoAwal->id = (string) Str::uuid();
            }
        });
    }

    /**
     * Get saldo awal for specific month and year
     */
    public static function getByPeriod($bulan, $tahun)
    {
        return self::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->first();
    }
}
