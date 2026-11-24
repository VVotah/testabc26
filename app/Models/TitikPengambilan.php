<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel titik_pengambilan (ERD 4.3.1): lokasi serah terima makanan milik mitra.
 * Dipakai modul lain lewat FK id_titik: listing (Ica) dan jadwal_pickup (Bitha).
 *
 * Diagram kelas 4.4.1: tambahTitik(), lihatDaftarTitik(), lihatDetailTitik(),
 * ubahTitik(), hapusTitik() dikerjakan TitikPengambilanController;
 * cekTitikDipakaiListing() ada di sini.
 *
 * Contoh untuk modul lain, pilihan titik milik mitra yang login:
 *   auth()->user()->mitra->titikPengambilan()->orderBy('nama_titik')->get()
 */
class TitikPengambilan extends Model
{
    protected $table = 'titik_pengambilan';

    protected $primaryKey = 'id_titik';

    protected $fillable = [
        'id_mitra',
        'nama_titik',
        'alamat',
        'patokan',
        'latitude',
        'longitude',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function mitra(): BelongsTo
    {
        return $this->belongsTo(Mitra::class, 'id_mitra', 'id_mitra');
    }

    /**
     * Jumlah listing berstatus Aktif yang memakai titik ini. Titik yang masih dipakai
     * tidak boleh dihapus (use case 4.1.3). Selama tabel listing (modul 2) belum ada,
     * hasilnya 0; begitu migration listing masuk, pengecekan ini langsung bekerja.
     */
    public function cekTitikDipakaiListing(): int
    {
        if (! Schema::hasTable('listing') || ! Schema::hasColumns('listing', ['id_titik', 'status_listing'])) {
            return 0;
        }

        return DB::table('listing')
            ->where('id_titik', $this->id_titik)
            ->where('status_listing', 'Aktif')
            ->count();
    }

    /** Koordinat untuk tampilan, mis. "-6.8915, 107.6107". */
    public function getKoordinatAttribute(): string
    {
        return self::formatKoordinat($this->latitude).', '.self::formatKoordinat($this->longitude);
    }

    /** Tautan ke peta OpenStreetMap di titik ini (tautan biasa, tanpa package peta). */
    public function getUrlPetaAttribute(): string
    {
        return sprintf(
            'https://www.openstreetmap.org/?mlat=%1$s&mlon=%2$s#map=18/%1$s/%2$s',
            self::formatKoordinat($this->latitude),
            self::formatKoordinat($this->longitude),
        );
    }

    private static function formatKoordinat(?float $nilai): string
    {
        return rtrim(rtrim(number_format((float) $nilai, 7, '.', ''), '0'), '.');
    }
}
