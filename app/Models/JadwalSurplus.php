<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JadwalSurplus extends Model
{
    protected $table = 'jadwal_surplus';

    protected $primaryKey = 'id_jadwal_surplus';

    protected $fillable = [
        'id_mitra',
        'hari',
        'jam',
        'estimasi_porsi',
    ];

    protected function casts(): array
    {
        return [
            'estimasi_porsi' => 'integer',
        ];
    }

    public function mitra(): BelongsTo
    {
        return $this->belongsTo(
            Mitra::class,
            'id_mitra',
            'id_mitra'
        );
    }
}
