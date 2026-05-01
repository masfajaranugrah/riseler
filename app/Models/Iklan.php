<?php
// app/Models/Iklan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Iklan extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'title',
        'message',
        'image',
        'type', 
        'total_sent',
        'status',
        'sent_at',
        'created_by'
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'total_sent' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });

        static::deleting(function ($iklan) {
            if ($iklan->image && Storage::disk('public')->exists($iklan->image)) {
                Storage::disk('public')->delete($iklan->image);
            }
        });
    }

    // Status methods
    public function isDraft()
    {
        return $this->status === 'draft';
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    // ? Type methods
    public function isInformasi()
    {
        return $this->type === 'informasi';
    }

    public function isMaintenance()
    {
        return $this->type === 'maintenance';
    }

    public function isIklan()
    {
        return $this->type === 'iklan';
    }

    // ? Type color attribute
    public function getTypeColorAttribute()
    {
        return match($this->type) {
            'informasi' => 'info',
            'maintenance' => 'warning',
            'iklan' => 'success',
            default => 'secondary'
        };
    }

    // ? Type icon attribute
    public function getTypeIconAttribute()
    {
        return match($this->type) {
            'informasi' => 'ri-information-line',
            'maintenance' => 'ri-tools-line',
            'iklan' => 'ri-megaphone-line',
            default => 'ri-notification-line'
        };
    }

    // ? Type label attribute
    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'informasi' => 'Informasi',
            'maintenance' => 'Maintenance',
            'iklan' => 'Iklan/Promosi',
            default => 'Unknown'
        };
    }

    // Status attributes
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'draft' => 'secondary',
            'active' => 'success',
            default => 'secondary'
        };
    }

    public function getStatusIconAttribute()
    {
        return match($this->status) {
            'draft' => 'ri-draft-line',
            'active' => 'ri-checkbox-circle-line',
            default => 'ri-question-line'
        };
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }
}
