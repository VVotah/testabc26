<?php

namespace Database\Seeders;

use App\Models\KategoriMakanan;
use App\Models\Listing;
use App\Models\Mitra;
use App\Models\Pengguna;
use App\Models\TitikPengambilan;
use Illuminate\Database\Seeder;

class ListingDonasiSeeder extends Seeder
{
    public function run(): void
    {
        $daftarKategori = [
            'Makanan berat',
            'Roti & kue',
            'Buah & sayur',
            'Minuman',
            'Lainnya',
        ];

        foreach ($daftarKategori as $namaKategori) {
            KategoriMakanan::firstOrCreate([
                'nama_kategori' => $namaKategori,
            ]);
        }

        // Ambil mitra Warung Bu Sari.
        $pengguna = Pengguna::where('email', 'mitra@foodloop.test')->first();

        if (! $pengguna) {
            $this->command?->warn('ListingDonasiSeeder: akun Warung Bu Sari tidak ditemukan.');

            return;
        }

        $mitra = Mitra::where('id_pengguna', $pengguna->id_pengguna)->first();

        if (! $mitra) {
            $this->command?->warn('ListingDonasiSeeder: data mitra Warung Bu Sari tidak ditemukan.');

            return;
        }

        $daftarListing = [
            [
                'nama_makanan' => 'Nasi dengan Lauk',
                'kategori' => 'Makanan berat',
                'titik' => 'Warung Utama',
                'jumlah_porsi' => 20,
                'tipe_listing' => Listing::TIPE_GRATIS,
                'harga' => null,
                'hari_kedaluwarsa' => 2,
            ],
            [
                'nama_makanan' => 'Nasi Goreng Spesial',
                'kategori' => 'Makanan berat',
                'titik' => 'Dapur Produksi',
                'jumlah_porsi' => 15,
                'tipe_listing' => Listing::TIPE_DISKON,
                'harga' => 12000,
                'hari_kedaluwarsa' => 3,
            ],
            [
                'nama_makanan' => 'Nasi Goreng Pedas',
                'kategori' => 'Makanan berat',
                'titik' => 'Gerai Cabang Dago',
                'jumlah_porsi' => 10,
                'tipe_listing' => Listing::TIPE_GRATIS,
                'harga' => null,
                'hari_kedaluwarsa' => 2,
            ],
        ];

        foreach ($daftarListing as $data) {
            $kategori = KategoriMakanan::where(
                'nama_kategori',
                $data['kategori']
            )->first();

            $titik = TitikPengambilan::where('id_mitra', $mitra->id_mitra)
                ->where('nama_titik', $data['titik'])
                ->first();

            if (! $kategori || ! $titik) {
                $this->command?->warn(
                    "ListingDonasiSeeder: kategori atau titik {$data['titik']} tidak ditemukan."
                );

                continue;
            }

            Listing::updateOrCreate(
                [
                    'id_mitra' => $mitra->id_mitra,
                    'nama_makanan' => $data['nama_makanan'],
                ],
                [
                    'id_titik' => $titik->id_titik,
                    'id_kategori_makanan' => $kategori->id_kategori_makanan,
                    // Kolom foto wajib terisi; listing contoh tidak punya berkas foto,
                    // jadi tampilannya memakai keadaan "Belum ada foto".
                    'foto' => '',
                    'jumlah_porsi' => $data['jumlah_porsi'],
                    'tipe_listing' => $data['tipe_listing'],
                    'harga' => $data['harga'],
                    'batas_kedaluwarsa' => now()
                        ->addDays($data['hari_kedaluwarsa']),
                    'status_listing' => Listing::STATUS_AKTIF,
                ]
            );
        }
    }
}
