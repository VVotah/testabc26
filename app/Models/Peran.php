<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Tabel peran (ERD 4.3.1).
 */
class Peran extends Model
{
    // Nama peran baku, sama persis dengan isi PeranSeeder.
    public const ADMIN = 'Admin';
    public const MITRA = 'Mitra/Donatur';
    public const PENERIMA = 'Penerima';
    public const RELAWAN = 'Relawan';

    protected $table = 'peran';

    protected $primaryKey = 'id_peran';

    protected $fillable = [
        'nama_peran',
        'keterangan',
    ];

    public function pengguna(): HasMany
    {
        return $this->hasMany(Pengguna::class, 'id_peran', 'id_peran');
    }
}
