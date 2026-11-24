<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * File bersama (milik Nug). Anggota lain cukup mengisi seeder modulnya sendiri,
 * tidak perlu mengedit file ini.
 */
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seeder modul, urut sesuai ketergantungan data (modul 1 sampai 7).
     * Seeder yang kelasnya belum ada di branch ini dilewati.
     */
    private const SEEDER_MODUL = [
        'AkunMitraSeeder',            // 1 Akun & Mitra (Nug)
        'ListingDonasiSeeder',        // 2 Listing Donasi (Ica)
        'PencarianPermintaanSeeder',  // 3 Pencarian & Permintaan (Nico)
        'PickupLogistikSeeder',       // 4 Pickup & Logistik (Bitha)
        'ForumSharingSeeder',         // 5 Forum Sharing (Intan)
        'EdukasiDampakSeeder',        // 6 Edukasi & Dampak (Rhein)
        'ModerasiKualitasSeeder',     // 7 Moderasi & Kualitas (Clara)
    ];

    public function run(): void
    {
        $this->call([
            PeranSeeder::class,
            AkunContohSeeder::class,
        ]);

        foreach (self::SEEDER_MODUL as $namaSeeder) {
            $kelas = __NAMESPACE__.'\\'.$namaSeeder;

            if (class_exists($kelas)) {
                $this->call($kelas);
            }
        }
    }
}
