<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Listing extends Model
{
    public const TIPE_GRATIS = 'Gratis';
    public const TIPE_DISKON = 'Diskon';

    public const TIPE = [
        self::TIPE_GRATIS,
        self::TIPE_DISKON,
    ];

    public const STATUS_AKTIF = 'Aktif';
    public const STATUS_DITUTUP = 'Ditutup';
    public const STATUS_HABIS = 'Habis';
    public const STATUS_KEDALUWARSA = 'Kedaluwarsa';
    public const STATUS_DITANGGUHKAN = 'Ditangguhkan';

    protected $table = 'listing';

    protected $primaryKey = 'id_listing';

    protected $fillable = [
        'id_mitra',
        'id_titik',
        'id_kategori_makanan',
        'nama_makanan',
        'foto',
        'jumlah_porsi',
        'tipe_listing',
        'harga',
        'batas_kedaluwarsa',
        'status_listing',
    ];

    protected function casts(): array
    {
        return [
            'jumlah_porsi' => 'integer',
            'harga' => 'decimal:2',
            'batas_kedaluwarsa' => 'datetime',
        ];
    }

    public function mitra(): BelongsTo
    {
        return $this->belongsTo(Mitra::class, 'id_mitra', 'id_mitra');
    }

    public function titikPengambilan(): BelongsTo
    {
        return $this->belongsTo(
            TitikPengambilan::class,
            'id_titik',
            'id_titik'
        );
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(
            KategoriMakanan::class,
            'id_kategori_makanan',
            'id_kategori_makanan'
        );
    }

    public function getFotoUrlAttribute(): ?string
{
    return $this->foto && Storage::disk('public')->exists($this->foto)
        ? '/storage/'.$this->foto
        : null;
}

    public function getTipeHargaTeksAttribute(): string
    {
        if ($this->tipe_listing === self::TIPE_DISKON && $this->harga !== null) {
            return 'Diskon · Rp '.number_format((float) $this->harga, 0, ',', '.');
        }

        return $this->tipe_listing;
    }
}