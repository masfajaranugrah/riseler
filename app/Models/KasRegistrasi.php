<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KasRegistrasi extends Model
{
    use HasFactory;

    protected $table = 'kas_registrasis';

    protected $fillable = [
        'keterangan',
        'pemasukan',
        'pengeluaran',
        'tanggal',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'pemasukan' => 'decimal:2',
        'pengeluaran' => 'decimal:2',
    ];
}
