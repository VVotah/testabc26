<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TindakLanjut extends Model
{
    protected $table = 'tindak_lanjut';

    protected $primaryKey = 'id_tindak_lanjut';

    public $timestamps = false;

    protected $fillable = [
        'id_laporan_masalah',
        'id_admin',
        'jenis_tindakan',
        'objek_ditangguhkan',
        'id_objek_ditangguhkan',
        'balasan_pelapor',
        'waktu_tindak_lanjut',
    ];

    protected $casts = [
        'id_laporan_masalah' => 'integer',
        'id_admin' => 'integer',
        'id_objek_ditangguhkan' => 'integer',
        'waktu_tindak_lanjut' => 'datetime',
    ];

    public function laporanMasalah(): BelongsTo
    {
        return $this->belongsTo(
            LaporanMasalah::class,
            'id_laporan_masalah',
            'id_laporan_masalah'
        );
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(
            Pengguna::class,
            'id_admin',
            'id_pengguna'
        );
    }
}
