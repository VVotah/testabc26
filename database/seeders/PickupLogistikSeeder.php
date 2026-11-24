<?php

namespace Database\Seeders;

use App\Models\JadwalPickup;
use App\Models\KonfirmasiSerahTerima;
use App\Models\Pengguna;
use App\Models\TitikPengambilan;
use Illuminate\Database\Seeder;

class PickupLogistikSeeder extends Seeder
{
    public function run(): void
    {
        $donatur = Pengguna::where('email', 'mitra@foodloop.test')->first();
        $penerima = Pengguna::whereHas('peran', fn ($q) => $q->where('nama_peran', 'Penerima'))->first();
        $titik = TitikPengambilan::whereHas('mitra', fn ($q) => $q->where('id_pengguna', $donatur?->id_pengguna))->first()
            ?? TitikPengambilan::first();

        if (! $donatur || ! $penerima || ! $titik) {
            return;
        }

        $jadwal = JadwalPickup::firstOrCreate(
            [
                'id_donatur' => $donatur->id_pengguna,
                'id_penerima' => $penerima->id_pengguna,
                'id_titik' => $titik->id_titik,
            ],
            [
                'id_permintaan' => null,
                'slot_waktu' => now()->addHours(2)->startOfHour(),
                'status_jadwal' => 'Dijadwalkan',
            ]
        );

        KonfirmasiSerahTerima::firstOrCreate(
            ['kode_pickup' => 'FL-1001'],
            [
                'id_jadwal_pickup' => $jadwal->id_jadwal_pickup,
                'status_serah_terima' => 'Menunggu',
            ]
        );

        // Satu jadwal yang menunggu persetujuan donatur, untuk contoh tombol Setujui/Tolak.
        $jadwalMenunggu = JadwalPickup::firstOrCreate(
            [
                'id_donatur' => $donatur->id_pengguna,
                'id_penerima' => $penerima->id_pengguna,
                'id_titik' => $titik->id_titik,
                'status_jadwal' => 'Menunggu',
            ],
            [
                'id_permintaan' => null,
                'slot_waktu' => now()->addDay()->setTime(10, 0),
            ]
        );

        KonfirmasiSerahTerima::firstOrCreate(
            ['kode_pickup' => 'FL-1002'],
            [
                'id_jadwal_pickup' => $jadwalMenunggu->id_jadwal_pickup,
                'status_serah_terima' => 'Menunggu',
            ]
        );
    }
}
