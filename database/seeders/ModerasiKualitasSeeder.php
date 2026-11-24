<?php

namespace Database\Seeders;

use App\Models\LaporanMasalah;
use App\Models\Listing;
use App\Models\Notifikasi;
use App\Models\Pengguna;
use App\Models\PostinganSharing;
use App\Models\TindakLanjut;
use Illuminate\Database\Seeder;

class ModerasiKualitasSeeder extends Seeder
{
    public function run(): void
    {
        $pengguna = Pengguna::where('status_akun', 'Aktif')->first();

        if (! $pengguna) {
            $this->command->warn(
                'ModerasiKualitasSeeder dilewati karena belum ada pengguna aktif.'
            );

            return;
        }

        Notifikasi::firstOrCreate([
            'id_pengguna' => $pengguna->id_pengguna,
            'jenis_perubahan' => 'Status Permintaan',
            'id_objek' => 1,
            'isi_notifikasi' => 'Status permintaan makanan kamu telah diperbarui.',
        ]);

        Notifikasi::firstOrCreate([
            'id_pengguna' => $pengguna->id_pengguna,
            'jenis_perubahan' => 'Status Pickup',
            'id_objek' => 1,
            'isi_notifikasi' => 'Status pickup makanan kamu telah diperbarui.',
        ]);

        $this->buatLaporanContoh();

        $this->command->info(
            'Seeder Moderasi & Kualitas berhasil dijalankan.'
        );
    }

    /**
     * Laporan masalah contoh supaya halaman Laporan Masuk Admin tidak kosong saat demo.
     * Tanpa bukti foto, karena berkas foto contoh tidak disimpan di repo.
     */
    private function buatLaporanContoh(): void
    {
        $pelapor = Pengguna::where('email', 'penerima@foodloop.test')->first();
        $admin = Pengguna::where('email', 'admin@foodloop.test')->first();

        if (! $pelapor || ! $admin) {
            return;
        }

        $listing = Listing::orderBy('id_listing')->first();
        $akun = Pengguna::where('email', 'panitia.festival@foodloop.test')->first();
        $postingan = PostinganSharing::orderBy('id_postingan_sharing')->first();

        if ($listing) {
            LaporanMasalah::firstOrCreate(
                [
                    'id_pelapor' => $pelapor->id_pengguna,
                    'jenis_objek' => LaporanMasalah::OBJEK_LISTING,
                    'id_objek' => $listing->id_listing,
                ],
                [
                    'kategori_masalah' => 'Kedaluwarsa',
                    'deskripsi_masalah' => 'Makanan sudah berbau saat diambil, padahal batas kedaluwarsanya masih besok.',
                    'status_laporan' => LaporanMasalah::STATUS_BARU,
                    'waktu_laporan' => now()->subHours(2),
                ]
            );
        }

        if ($akun) {
            LaporanMasalah::firstOrCreate(
                [
                    'id_pelapor' => $pelapor->id_pengguna,
                    'jenis_objek' => LaporanMasalah::OBJEK_AKUN,
                    'id_objek' => $akun->id_pengguna,
                ],
                [
                    'kategori_masalah' => 'Spam',
                    'deskripsi_masalah' => 'Akun ini berkali-kali mengirim pesan promosi yang tidak berhubungan dengan donasi.',
                    'status_laporan' => LaporanMasalah::STATUS_BARU,
                    'waktu_laporan' => now()->subDay(),
                ]
            );
        }

        if ($postingan) {
            $laporan = LaporanMasalah::firstOrCreate(
                [
                    'id_pelapor' => $pelapor->id_pengguna,
                    'jenis_objek' => LaporanMasalah::OBJEK_POSTINGAN_SHARING,
                    'id_objek' => $postingan->id_postingan_sharing,
                ],
                [
                    'kategori_masalah' => 'Tidak sesuai',
                    'status_laporan' => LaporanMasalah::STATUS_DITINJAU,
                    'waktu_laporan' => now()->subDays(2),
                ]
            );

            TindakLanjut::firstOrCreate(
                [
                    'id_laporan_masalah' => $laporan->id_laporan_masalah,
                    'id_admin' => $admin->id_pengguna,
                ],
                [
                    'jenis_tindakan' => 'Pemeriksaan laporan',
                    'balasan_pelapor' => 'Terima kasih, laporan kamu sedang kami periksa.',
                    'waktu_tindak_lanjut' => now()->subDay(),
                ]
            );
        }
    }
}
