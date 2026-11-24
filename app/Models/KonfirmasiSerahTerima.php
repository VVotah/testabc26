<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KonfirmasiSerahTerima extends Model
{
    protected $table = 'konfirmasi_serah_terima';

    protected $primaryKey = 'id_konfirmasi';

    protected $fillable = [
        'id_jadwal_pickup',
        'kode_pickup',
        'status_serah_terima',
    ];

    public function jadwalPickup(): BelongsTo
    {
        return $this->belongsTo(JadwalPickup::class, 'id_jadwal_pickup', 'id_jadwal_pickup');
    }
}
