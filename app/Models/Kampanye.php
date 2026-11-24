<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kampanye extends Model
{
    public const AKAN_DATANG = 'Akan Datang';
    public const BERLANGSUNG = 'Berlangsung';
    public const SELESAI = 'Selesai';
    public const DIBATALKAN = 'Dibatalkan';

    public const STATUS = [self::AKAN_DATANG, self::BERLANGSUNG, self::SELESAI, self::DIBATALKAN];

    // waktu_dibuat menggantikan created_at, tanpa updated_at (ASUMSI)
    public const CREATED_AT = 'waktu_dibuat';
    public const UPDATED_AT = null;

    protected $table = 'kampanye';
    protected $primaryKey = 'id_kampanye';

    protected $fillable = [
        'id_pembuat', 'nama_kampanye', 'deskripsi', 'lokasi',
        'tanggal_mulai', 'tanggal_selesai', 'status_kampanye', 'waktu_dibuat',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'waktu_dibuat' => 'datetime',
    ];

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_pembuat', 'id_pengguna');
    }

    public function scopeBelumSelesai(Builder $query): Builder
    {
        return $query->whereIn('status_kampanye', [self::AKAN_DATANG, self::BERLANGSUNG]);
    }

    // Hanya Akan Datang dan Berlangsung yang boleh diubah atau dibatalkan (ASUMSI)
    public function bisaDiubah(): bool
    {
        return in_array($this->status_kampanye, [self::AKAN_DATANG, self::BERLANGSUNG], true);
    }

    public function bisaDibatalkan(): bool
    {
        return $this->bisaDiubah();
    }

    public function batalkan(): void
    {
        $this->update(['status_kampanye' => self::DIBATALKAN]);
    }

    /**
     * Sinkronkan status dengan tanggal pelaksanaan (ASUMSI).
     * Kampanye yang Dibatalkan tidak pernah diubah.
     */
    public static function sinkronStatus(): void
    {
        $hariIni = today()->toDateString();

        static::where('status_kampanye', '!=', self::DIBATALKAN)
            ->where('tanggal_selesai', '<', $hariIni)
            ->update(['status_kampanye' => self::SELESAI]);

        static::where('status_kampanye', '!=', self::DIBATALKAN)
            ->where('tanggal_mulai', '<=', $hariIni)
            ->where('tanggal_selesai', '>=', $hariIni)
            ->update(['status_kampanye' => self::BERLANGSUNG]);

        static::where('status_kampanye', '!=', self::DIBATALKAN)
            ->where('tanggal_mulai', '>', $hariIni)
            ->update(['status_kampanye' => self::AKAN_DATANG]);
    }
}