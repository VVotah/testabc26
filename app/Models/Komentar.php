<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Komentar extends Model
{
    public const CREATED_AT = 'waktu_komentar';
    public const UPDATED_AT = null;

    protected $table = 'komentar';
    protected $primaryKey = 'id_komentar';

    protected $fillable = [
        'id_postingan_sharing',
        'id_penulis',
        'isi_komentar',
    ];

    protected $casts = [
        'waktu_komentar' => 'datetime',
    ];

    public function postingan(): BelongsTo
    {
        return $this->belongsTo(
            PostinganSharing::class,
            'id_postingan_sharing',
            'id_postingan_sharing'
        );
    }

    public function penulis(): BelongsTo
    {
        return $this->belongsTo(
            Pengguna::class,
            'id_penulis',
            'id_pengguna'
        );
    }
}
