<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Artikel extends Model
{
    public const DRAF = 'Draf';
    public const TERBIT = 'Terbit';
    public const DIARSIPKAN = 'Diarsipkan';

    public const STATUS = [self::DRAF, self::TERBIT, self::DIARSIPKAN];

    // waktu_dibuat menggantikan created_at, tanpa updated_at (ASUMSI)
    public const CREATED_AT = 'waktu_dibuat';
    public const UPDATED_AT = null;

    protected $table = 'artikel';
    protected $primaryKey = 'id_artikel';

    protected $fillable = [
        'id_penulis', 'judul', 'isi', 'gambar',
        'status_artikel', 'waktu_dibuat', 'waktu_terbit',
    ];

    protected $casts = [
        'waktu_dibuat' => 'datetime',
        'waktu_terbit' => 'datetime',
    ];

    public function penulis(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_penulis', 'id_pengguna');
    }

    public function scopeTerbit(Builder $query): Builder
    {
        return $query->where('status_artikel', self::TERBIT);
    }

    public function bisaDiubah(): bool
    {
        return $this->status_artikel !== self::DIARSIPKAN;
    }

    public function bisaDiterbitkan(): bool
    {
        return $this->status_artikel === self::DRAF;
    }

    public function bisaDiarsipkan(): bool
    {
        return $this->status_artikel === self::TERBIT;
    }

    public function terbitkan(): void
    {
        $this->update([
            'status_artikel' => self::TERBIT,
            'waktu_terbit' => now(),
        ]);
    }

    public function arsipkan(): void
    {
        $this->update(['status_artikel' => self::DIARSIPKAN]);
    }

    public function getUrlGambarAttribute(): ?string
    {
        return $this->gambar ? Storage::disk('public')->url($this->gambar) : null;
    }

    public function getCuplikanAttribute(): string
    {
        return Str::limit(strip_tags((string) $this->isi), 140);
    }
}