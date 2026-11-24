<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class JadwalPickup extends Model
{
    protected $table = 'jadwal_pickup';

    protected $primaryKey = 'id_jadwal_pickup';

    protected $fillable = [
        'id_donatur',
        'id_penerima',
        'id_permintaan',
        'slot_waktu',
        'id_titik',
        'status_jadwal',
    ];

    protected function casts(): array
    {
        return [
            'slot_waktu' => 'datetime',
        ];
    }

    public function donatur(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_donatur', 'id_pengguna');
    }

    public function penerima(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_penerima', 'id_pengguna');
    }

    public function permintaan(): BelongsTo
    {
        return $this->belongsTo(Permintaan::class, 'id_permintaan', 'id_permintaan');
    }

    public function titikPengambilan(): BelongsTo
    {
        return $this->belongsTo(TitikPengambilan::class, 'id_titik', 'id_titik');
    }

    public function konfirmasiSerahTerima(): HasOne
    {
        return $this->hasOne(KonfirmasiSerahTerima::class, 'id_jadwal_pickup', 'id_jadwal_pickup');
    }
}