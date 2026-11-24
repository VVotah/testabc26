<?php

namespace Database\Seeders;

use App\Models\Artikel;
use App\Models\Kampanye;
use App\Models\Pengguna;
use Illuminate\Database\Seeder;

/**
 * Data contoh Modul 6 Edukasi & Dampak (pemilik: Rhein).
 * Dipanggil otomatis dari DatabaseSeeder, jadi cukup isi run() di sini.
 */
class EdukasiDampakSeeder extends Seeder
{
    public function run(): void
    {
        // Akun Admin contoh dibuat oleh AkunContohSeeder (hanya APP_ENV=local).
        $admin = Pengguna::where('email', 'admin@foodloop.test')->first();

        if (! $admin) {
            return;
        }

        $this->artikel($admin);
        $this->kampanye($admin);
    }

    private function artikel(Pengguna $admin): void
    {
        $contoh = [
            [
                'judul' => 'Cara Menyimpan Sayur agar Tidak Cepat Layu',
                'isi' => "Sayur yang disimpan dengan benar akan bertahan lebih lama dan mengurangi makanan terbuang di rumah.\n\nSimpan sayur daun di wadah tertutup yang dialasi tisu dapur, dan pisahkan dari buah yang cepat matang.",
                'status_artikel' => Artikel::TERBIT,
                'waktu_dibuat' => now()->subDays(10),
                'waktu_terbit' => now()->subDays(9),
            ],
            [
                'judul' => 'Mengenal Dampak Food Waste bagi Lingkungan',
                'isi' => "Makanan yang berakhir di tempat sampah ikut menghasilkan gas metana saat membusuk.\n\nDengan menyalurkan makanan layak konsumsi kepada yang membutuhkan, kita membantu mengurangi dampak tersebut.",
                'status_artikel' => Artikel::TERBIT,
                'waktu_dibuat' => now()->subDays(6),
                'waktu_terbit' => now()->subDays(5),
            ],
            [
                'judul' => 'Tips Belanja Hemat Tanpa Sisa',
                'isi' => "Buat daftar belanja sebelum berangkat dan cek persediaan di rumah terlebih dahulu.\n\nBeli sesuai kebutuhan mingguan agar tidak ada bahan makanan yang terbuang.",
                'status_artikel' => Artikel::TERBIT,
                'waktu_dibuat' => now()->subDays(3),
                'waktu_terbit' => now()->subDays(2),
            ],
            [
                'judul' => 'Memanfaatkan Nasi Sisa Menjadi Hidangan Baru',
                'isi' => null, // contoh Draf yang belum lengkap
                'status_artikel' => Artikel::DRAF,
                'waktu_dibuat' => now()->subDay(),
                'waktu_terbit' => null,
            ],
            [
                'judul' => 'Panduan Lama Mengatur Kulkas',
                'isi' => 'Artikel lama yang sudah tidak relevan.',
                'status_artikel' => Artikel::DIARSIPKAN,
                'waktu_dibuat' => now()->subDays(30),
                'waktu_terbit' => now()->subDays(29),
            ],
        ];

        foreach ($contoh as $data) {
            Artikel::firstOrCreate(
                ['judul' => $data['judul']],
                $data + ['id_penulis' => $admin->id_pengguna]
            );
        }
    }

    private function kampanye(Pengguna $admin): void
    {
        $contoh = [
            [
                'nama_kampanye' => 'Pekan Habiskan Makananmu',
                'deskripsi' => 'Ajakan menghabiskan makanan di piring dan membagikan porsi lebih kepada yang membutuhkan selama satu pekan.',
                'lokasi' => 'Kampus Telkom University, Bandung',
                'tanggal_mulai' => today()->subDays(2)->toDateString(),
                'tanggal_selesai' => today()->addDays(4)->toDateString(),
                'status_kampanye' => Kampanye::BERLANGSUNG,
            ],
            [
                'nama_kampanye' => 'Aksi Selamatkan Makanan Bandung',
                'deskripsi' => 'Pengumpulan makanan berlebih dari kantin dan restoran untuk disalurkan kepada komunitas sekitar.',
                'lokasi' => 'Alun-alun Bandung',
                'tanggal_mulai' => today()->addDays(10)->toDateString(),
                'tanggal_selesai' => today()->addDays(12)->toDateString(),
                'status_kampanye' => Kampanye::AKAN_DATANG,
            ],
            [
                'nama_kampanye' => 'Bazar Sisa Roti',
                'deskripsi' => 'Bazar roti sisa produksi dengan harga diskon dari toko roti mitra.',
                'lokasi' => 'Cimahi',
                'tanggal_mulai' => today()->subDays(20)->toDateString(),
                'tanggal_selesai' => today()->subDays(19)->toDateString(),
                'status_kampanye' => Kampanye::SELESAI,
            ],
            [
                'nama_kampanye' => 'Panen Sayur Bersama',
                'deskripsi' => 'Kegiatan memanen sayur untuk dibagikan, dibatalkan karena cuaca.',
                'lokasi' => 'Lembang',
                'tanggal_mulai' => today()->addDays(6)->toDateString(),
                'tanggal_selesai' => today()->addDays(6)->toDateString(),
                'status_kampanye' => Kampanye::DIBATALKAN,
            ],
        ];

        foreach ($contoh as $data) {
            Kampanye::firstOrCreate(
                ['nama_kampanye' => $data['nama_kampanye']],
                $data + ['id_pembuat' => $admin->id_pengguna]
            );
        }
    }
}