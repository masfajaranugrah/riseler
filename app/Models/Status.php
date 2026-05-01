<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'statuses';

    protected $fillable = [
        'pelanggan_id',
        'is_active',
        'logged_in_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'logged_in_at' => 'datetime',
    ];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id');
    }
}
