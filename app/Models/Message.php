<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class Message extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'chat_type',
        'message',
        'media_path',
        'media_type',
        'media_original_name',
        'is_read',
        'is_deleted',
        'edited_at',
        'deleted_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_deleted' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'edited_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $appends = ['sender', 'receiver', 'media_url'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /**
     * Get sender - could be from users or pelanggans table
     */
    public function getSenderAttribute()
    {
        $cacheKey = "message_sender_{$this->sender_id}";

        try {
            return Cache::remember($cacheKey, 300, function () {
                $user = User::find($this->sender_id);
                if ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email ?? null,
                        'role' => $user->role ?? null,
                    ];
                }

                $pelanggan = Pelanggan::find($this->sender_id);
                if ($pelanggan) {
                    return [
                        'id' => $pelanggan->id,
                        'name' => $pelanggan->nama_lengkap ?? $pelanggan->name ?? 'Pelanggan',
                        'email' => $pelanggan->email ?? null,
                        'role' => 'pelanggan',
                    ];
                }

                return null;
            });
        } catch (\Exception $e) {
            // Fallback: query directly without cache if Redis/DB cache fails
            $user = User::find($this->sender_id);
            if ($user) {
                return ['id' => $user->id, 'name' => $user->name, 'email' => $user->email ?? null, 'role' => $user->role ?? null];
            }
            $pelanggan = Pelanggan::find($this->sender_id);
            if ($pelanggan) {
                return ['id' => $pelanggan->id, 'name' => $pelanggan->nama_lengkap ?? 'Pelanggan', 'email' => $pelanggan->email ?? null, 'role' => 'pelanggan'];
            }
            return null;
        }
    }

    /**
     * Get receiver - could be from users or pelanggans table
     */
    public function getReceiverAttribute()
    {
        $cacheKey = "message_receiver_{$this->receiver_id}";

        try {
            return Cache::remember($cacheKey, 300, function () {
                $user = User::find($this->receiver_id);
                if ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email ?? null,
                        'role' => $user->role ?? null,
                    ];
                }

                $pelanggan = Pelanggan::find($this->receiver_id);
                if ($pelanggan) {
                    return [
                        'id' => $pelanggan->id,
                        'name' => $pelanggan->nama_lengkap ?? $pelanggan->name ?? 'Pelanggan',
                        'email' => $pelanggan->email ?? null,
                        'role' => 'pelanggan',
                    ];
                }

                return null;
            });
        } catch (\Exception $e) {
            // Fallback: query directly without cache if Redis/DB cache fails
            $user = User::find($this->receiver_id);
            if ($user) {
                return ['id' => $user->id, 'name' => $user->name, 'email' => $user->email ?? null, 'role' => $user->role ?? null];
            }
            $pelanggan = Pelanggan::find($this->receiver_id);
            if ($pelanggan) {
                return ['id' => $pelanggan->id, 'name' => $pelanggan->nama_lengkap ?? 'Pelanggan', 'email' => $pelanggan->email ?? null, 'role' => 'pelanggan'];
            }
            return null;
        }
    }

    /**
     * Get media URL accessor
     */
    public function getMediaUrlAttribute()
    {
        if ($this->media_path) {
            // Use relative path that works on any domain
            return '/storage/' . $this->media_path;
        }
        return null;
    }
}
