<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanKabel extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_pelanggan',
        'wilayah',
        'employee_id',
        'alamat',
        'tarikan_meter',
        'jenis_kabel',
        'sisi_core',
        'keterangan',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
