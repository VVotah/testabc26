<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriMakanan extends Model
{
    protected $table = 'kategori_makanan';

    protected $primaryKey = 'id_kategori_makanan';

    protected $fillable = [
        'nama_kategori',
    ];

    public function listings(): HasMany
    {
        return $this->hasMany(
            Listing::class,
            'id_kategori_makanan',
            'id_kategori_makanan'
        );
    }
}
