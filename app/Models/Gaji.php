<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Gaji extends Model
{
    use HasFactory;

    protected $table = 'gaji';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'employee_id', 'gaji_pokok', 'tunj_jabatan', 'tunj_fungsional',
        'transport', 'makan', 'tunj_dynamic', 'tunj_keterangan', 'tunj_kehadiran',
        'lembur', 'pot_sosial', 'pot_denda', 'pot_koperasi', 'pot_pajak', 'pot_lain',
        'total', 'grand_total'
    ];

    protected $casts = [
        'tunj_dynamic' => 'array',
        'tunj_keterangan' => 'array'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }


    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
