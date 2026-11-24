<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PeranSeeder extends Seeder
{
    /**
     * Isi data awal tabel peran (ERD 4.3.1).
     */
    public function run(): void
    {
        $now = now();

        DB::table('peran')->insert([
            [
                'nama_peran' => 'Admin',
                'keterangan' => 'Mengelola verifikasi mitra, moderasi laporan, serta konten edukasi dan kampanye.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_peran' => 'Mitra/Donatur',
                'keterangan' => 'Mengelola profil usaha, titik pengambilan, listing makanan, dan jadwal surplus.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_peran' => 'Penerima',
                'keterangan' => 'Mencari listing, mengajukan permintaan, dan mengikuti mitra.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_peran' => 'Relawan',
                'keterangan' => 'Mengambil dan menyelesaikan tugas pengantaran makanan.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
