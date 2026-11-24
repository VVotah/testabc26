<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KlaimPorsi extends Model
{
    public const STATUS_MENUNGGU = 'Menunggu Persetujuan';
    public const STATUS_DISETUJUI = 'Disetujui';
    public const STATUS_DITOLAK = 'Ditolak';
    public const STATUS_DIBATALKAN = 'Dibatalkan';

    public const CREATED_AT = 'waktu_pengajuan';
    public const UPDATED_AT = null;

    protected $table = 'klaim_porsi';
    protected $primaryKey = 'id_klaim_porsi';

    protected $fillable = [
        'id_postingan_sharing',
        'id_pengklaim',
        'jumlah_porsi',
        'status_klaim',
    ];

    protected $casts = [
        'jumlah_porsi' => 'integer',
        'waktu_pengajuan' => 'datetime',
    ];

    public function postingan(): BelongsTo
    {
        return $this->belongsTo(
            PostinganSharing::class,
            'id_postingan_sharing',
            'id_postingan_sharing'
        );
    }

    public function pengklaim(): BelongsTo
    {
        return $this->belongsTo(
            Pengguna::class,
            'id_pengklaim',
            'id_pengguna'
        );
    }
}
