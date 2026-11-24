<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $table = 'notifikasi';

    protected $primaryKey = 'id_notifikasi';

    public $timestamps = false;

    protected $fillable = [
        'id_pengguna',
        'jenis_perubahan',
        'id_objek',
        'isi_notifikasi',
        'waktu_notifikasi',
    ];

    protected $casts = [
        'id_objek' => 'integer',
        'waktu_notifikasi' => 'datetime',
    ];

    public function pengguna()
    {
        return $this->belongsTo(
            Pengguna::class,
            'id_pengguna',
            'id_pengguna'
        );
    }
}
