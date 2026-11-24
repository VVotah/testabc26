<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ulasan extends Model
{
    protected $table = 'ulasan';

    protected $primaryKey = 'id_ulasan';

    public $timestamps = false;

    protected $fillable = [
        'id_penerima',
        'id_donatur',
        'id_jadwal_pickup',
        'rating',
        'teks_ulasan',
        'waktu_ulasan',
    ];

    protected $casts = [
        'rating' => 'integer',
        'waktu_ulasan' => 'datetime',
    ];

    public function penerima(): BelongsTo
    {
        return $this->belongsTo(
            Pengguna::class,
            'id_penerima',
            'id_pengguna'
        );
    }

    public function donatur(): BelongsTo
    {
        return $this->belongsTo(
            Pengguna::class,
            'id_donatur',
            'id_pengguna'
        );
    }

    public function jadwalPickup(): BelongsTo
    {
        return $this->belongsTo(
            JadwalPickup::class,
            'id_jadwal_pickup',
            'id_jadwal_pickup'
        );
    }
}
