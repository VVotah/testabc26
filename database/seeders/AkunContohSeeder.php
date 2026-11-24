<?php

namespace Database\Seeders;

use App\Models\Pengguna;
use App\Models\Peran;
use Illuminate\Database\Seeder;

/**
 * Akun contoh, satu per peran. KHUSUS pengembangan lokal (APP_ENV=local),
 * tidak dibuat di server. Email dan kata sandi dicatat di CLAUDE.md.
 */
class AkunContohSeeder extends Seeder
{
    public const KATA_SANDI = 'password';

    public function run(): void
    {
        if (! app()->environment('local', 'testing')) {
            return;
        }

        $akun = [
            Peran::ADMIN => ['Admin Contoh', 'admin@foodloop.test'],
            Peran::MITRA => ['Mitra Contoh', 'mitra@foodloop.test'],
            Peran::PENERIMA => ['Penerima Contoh', 'penerima@foodloop.test'],
            Peran::RELAWAN => ['Relawan Contoh', 'relawan@foodloop.test'],
        ];

        foreach ($akun as $namaPeran => [$nama, $email]) {
            Pengguna::updateOrCreate(
                ['email' => $email],
                [
                    'id_peran' => Peran::where('nama_peran', $namaPeran)->value('id_peran'),
                    'nama' => $nama,
                    'kata_sandi' => self::KATA_SANDI, // di-hash otomatis oleh model
                    'status_akun' => Pengguna::STATUS_AKTIF,
                ],
            );
        }
    }
}
