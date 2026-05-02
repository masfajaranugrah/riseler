<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Hutang extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'nama_barang',
        'jumlah',
        'catatan',
        'tanggal',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($hutang) {
            if (! $hutang->id) {
                $hutang->id = (string) Str::uuid();
            }
        });
    }
}
