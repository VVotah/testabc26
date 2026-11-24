<?php

namespace Database\Seeders;

use App\Models\PostinganSharing;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ForumSharingSeeder extends Seeder
{
    public function run(): void
    {
        // Hanya MEMBACA tabel pengguna (milik Nug), tidak mengubahnya.
        $idPengguna = DB::table('pengguna')->orderBy('id_pengguna')->value('id_pengguna');

        if (! $idPengguna) {
            $this->command?->warn('ForumSharingSeeder dilewati: tabel pengguna masih kosong.');
            return;
        }

        $contoh = [
            ['Nasi Padang Sisa Acara', 12, 'Jl. Merdeka No. 5, Jakarta', now()->addHours(6)],
            ['Kue Tart Sisa Ulang Tahun', 8, 'Jl. Sudirman No. 12, Jakarta', now()->addHours(8)],
            ['Roti Tawar Sisa Bazar', 30, 'Jl. Gatot Subroto No. 3, Jakarta', now()->addDay()],
        ];

        foreach ($contoh as [$nama, $porsi, $lokasi, $batas]) {
            PostinganSharing::create([
                'id_pengunggah' => $idPengguna,
                'nama_makanan' => $nama,
                'foto_makanan' => 'postingan_sharing/contoh.jpg', // berkas tidak ada; tampilan memakai kotak abu "Foto makanan"
                'sisa_porsi' => $porsi,
                'lokasi_pengambilan' => $lokasi,
                'batas_waktu' => $batas,
                'status_postingan' => PostinganSharing::STATUS_AKTIF,
            ]);
        }
    }
}