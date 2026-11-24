<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PostinganSharing extends Model
{
    public const STATUS_AKTIF = 'Aktif';
    public const STATUS_HABIS = 'Habis';
    public const STATUS_DITUTUP = 'Ditutup';
    public const STATUS_KEDALUWARSA = 'Kedaluwarsa';
    public const STATUS_DITANGGUHKAN = 'Ditangguhkan';

    // ERD hanya punya waktu_dibuat, tidak ada updated_at.
    public const CREATED_AT = 'waktu_dibuat';
    public const UPDATED_AT = null;

    protected $table = 'postingan_sharing';
    protected $primaryKey = 'id_postingan_sharing';

    protected $fillable = [
        'id_pengunggah',
        'nama_makanan',
        'foto_makanan',
        'sisa_porsi',
        'lokasi_pengambilan',
        'batas_waktu',
        'status_postingan',
    ];

    protected $casts = [
        'batas_waktu' => 'datetime',
        'waktu_dibuat' => 'datetime',
    ];

    public function pengunggah()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengunggah', 'id_pengguna');
    }

    public function dimilikiOleh($idPengguna): bool
    {
        return (int) $this->id_pengunggah === (int) $idPengguna;
    }

    public function berstatusAktif(): bool
    {
        return $this->status_postingan === self::STATUS_AKTIF;
    }

    /** URL foto, atau null kalau berkas belum ada (mis. data seeder). */
    public function getFotoUrlAttribute(): ?string
    {
        if ($this->foto_makanan && Storage::disk('public')->exists($this->foto_makanan)) {
            return asset('storage/' . $this->foto_makanan);
        }

        return null;
    }

        public function klaimPorsi(): HasMany
    {
        return $this->hasMany(
            KlaimPorsi::class,
            'id_postingan_sharing',
            'id_postingan_sharing'
        );
    }

    public function komentar(): HasMany
    {
        return $this->hasMany(
            Komentar::class,
            'id_postingan_sharing',
            'id_postingan_sharing'
        );
    }
}
