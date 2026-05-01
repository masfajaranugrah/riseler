<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use NotificationChannels\WebPush\HasPushSubscriptions;
use App\Models\Status;

class Pelanggan extends Authenticatable
{
    use HasApiTokens, HasFactory, HasPushSubscriptions, HasUuids, Notifiable;

    public const STATUS_PROSES = 'pending';
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVE = 'approve';
    public const STATUS_REJECT = 'reject';

    public const PROGRES_BELUM_DIPROSES = 'Belum Diproses';
    public const PROGRES_TARIK_KABEL = 'Tarik Kabel';
    public const PROGRES_AKTIVASI = 'Aktivasi';
    public const PROGRES_REGISTRASI = 'Registrasi';

    public const PROGRES_STAGES = [
        self::PROGRES_BELUM_DIPROSES,
        self::PROGRES_TARIK_KABEL,
        self::PROGRES_AKTIVASI,
        self::PROGRES_REGISTRASI,
    ];

    protected $table = 'pelanggans';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'user_id',

        'nama_lengkap',
        'no_ktp',
        'no_whatsapp',
        'no_telp',

        'paket_id',
        'nomer_id',

        'tanggal_mulai',
        'tanggal_berakhir',
        'webpushr_sid',
        'deskripsi',
        'foto_ktp',
        'status',
        'progress_note',
        'progres',
        'alamat_jalan',
        'rt',
        'rw',
        'desa',
        'kecamatan',
        'kabupaten',
        'provinsi',
        'kode_pos',
    ];

    /**
     * ?? Relasi ke User (akun login)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * ?? Relasi ke Paket
     */
    public function paket()
    {
        return $this->belongsTo(Paket::class, 'paket_id');
    }

     /**
     * Status keaktifan pelanggan (login / logout).
     */
    public function loginStatus()
    {
        return $this->hasOne(Status::class, 'pelanggan_id');
    }


    /**
     * ?? Relasi ke Tagihan
     */
    public function tagihans()
    {
        return $this->hasMany(Tagihan::class);
    }

    /**
     * ?? Accessor untuk menampilkan URL foto KTP
     */
    public function getFotoKtpUrlAttribute()
    {
        return $this->foto_ktp ? Storage::url($this->foto_ktp) : null;
    }

    /**
     * ?? Scope untuk filter status pelanggan
     */
 public function scopeFilterStatus($query, $status)
{
    if (! empty($status)) {
        return $query->where('status', $status);
    }

    return $query;
}
    /**
     * ?? Scope pencarian global
     */
    public function scopeSearch($query, $term)
    {
        if (! empty($term)) {
            return $query->where(function ($q) use ($term) {
                $q->where('nama_lengkap', 'like', "%{$term}%")
                    ->orWhere('no_whatsapp', 'like', "%{$term}%")
                    ->orWhere('nomer_id', 'like', "%{$term}%")
                    ->orWhere('kabupaten', 'like', "%{$term}%");
            });
        }

        return $query;
    }
}
