<?php

namespace Database\Seeders;

use App\Models\Mitra;
use App\Models\Pengguna;
use App\Models\Peran;
use App\Models\TitikPengambilan;
use App\Models\VerifikasiMitra;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Data contoh Modul 1 Akun & Mitra (pemilik: Nug).
 * Dipanggil otomatis dari DatabaseSeeder, jadi cukup isi run() di sini.
 *
 * KHUSUS lokal:
 * - mitra@foodloop.test (Warung Bu Sari) sudah Terverifikasi, supaya modul lain
 *   (mis. listing) bisa langsung diuji tanpa lewat verifikasi Admin.
 * - Beberapa mitra lain berstatus Menunggu verifikasi dan Ditolak, supaya halaman
 *   Verifikasi Mitra milik Admin punya isi. Kata sandi semuanya "password".
 * - Dokumen bukti berupa PDF dummy satu halaman di disk privat.
 * - Warung Bu Sari punya tiga titik pengambilan contoh (mockup 4.5.10).
 */
class AkunMitraSeeder extends Seeder
{
    private const MITRA_CONTOH = [
        // email, penanggung jawab, nama usaha, jenis, alamat jalan, kota, kode pos, hari lalu diajukan, keputusan, alasan
        ['mitra@foodloop.test', null, 'Warung Bu Sari', 'Restoran', 'Jl. Dipatiukur No. 12, Coblong', 'Bandung', '40132', 3, VerifikasiMitra::KEPUTUSAN_SETUJU, null],
        ['bakery.rotikita@foodloop.test', 'Rudi Hartono', 'Bakery Roti Kita', 'Toko roti', 'Jl. Buah Batu No. 88', 'Bandung', '40265', 1, null, null],
        ['katering.nusantara@foodloop.test', 'Dewi Lestari', 'Katering Nusantara', 'Katering', 'Jl. Ir. H. Juanda No. 45, Dago', 'Bandung', '40135', 2, null, null],
        ['kantin.fri@foodloop.test', 'Agus Pratama', 'Kantin FRI', 'Kantin', 'Jl. Telekomunikasi No. 1, Dayeuhkolot', 'Kabupaten Bandung', '40257', 0, null, null],
        ['panitia.festival@foodloop.test', 'Nadia Putri', 'Panitia Festival Kuliner', 'Panitia acara', 'Jl. Cihampelas No. 160', 'Bandung', '40131', 5, VerifikasiMitra::KEPUTUSAN_TOLAK,
            'Dokumen yang diunggah tidak terbaca. Mohon unggah ulang foto KTP penanggung jawab yang lebih jelas.'],
    ];

    /** Titik pengambilan contoh untuk Warung Bu Sari: nama, alamat, patokan, latitude, longitude. */
    private const TITIK_CONTOH = [
        ['Warung Utama', 'Jl. Dipatiukur No. 12, Coblong, Bandung', 'Pintu depan, sebelah apotek', -6.8915000, 107.6159000],
        ['Dapur Produksi', 'Jl. Buah Batu No. 88, Bandung', 'Pintu belakang, dekat parkiran', -6.9395000, 107.6236000],
        ['Gerai Cabang Dago', 'Jl. Ir. H. Juanda No. 45, Bandung', 'Seberang minimarket', -6.8858000, 107.6134000],
    ];

    public function run(): void
    {
        if (! app()->environment('local', 'testing')) {
            return;
        }

        $admin = Pengguna::where('email', 'admin@foodloop.test')->first();
        $idPeranMitra = Peran::where('nama_peran', Peran::MITRA)->value('id_peran');

        foreach (self::MITRA_CONTOH as [$email, $penanggungJawab, $namaUsaha, $jenis, $jalan, $kota, $kodePos, $hariLalu, $keputusan, $alasan]) {
            // Akun mitra@foodloop.test dibuat AkunContohSeeder; akun lain dibuat di sini.
            $pengguna = $penanggungJawab
                ? Pengguna::updateOrCreate(['email' => $email], [
                    'id_peran' => $idPeranMitra,
                    'nama' => $penanggungJawab,
                    'kata_sandi' => AkunContohSeeder::KATA_SANDI,
                    'status_akun' => Pengguna::STATUS_AKTIF,
                ])
                : Pengguna::where('email', $email)->first();

            if (! $pengguna) {
                $this->command?->warn("AkunMitraSeeder: akun {$email} belum ada, dilewati.");

                continue;
            }

            $mitra = Mitra::updateOrCreate(['id_pengguna' => $pengguna->id_pengguna], [
                'nama_mitra' => $namaUsaha,
                'jenis_mitra' => $jenis,
                'alamat' => Mitra::gabungAlamat($jalan, $kota, $kodePos),
                'status_mitra' => $keputusan ?? Mitra::STATUS_MENUNGGU,
            ]);

            $berkas = 'verifikasi_mitra/contoh-'.$mitra->id_mitra.'.pdf';
            Storage::disk('local')->put($berkas, $this->pdfContoh($namaUsaha));

            $diajukan = now()->subDays($hariLalu)->setTime(8 + $hariLalu, 15);

            VerifikasiMitra::updateOrCreate(['id_mitra' => $mitra->id_mitra], [
                'bukti_pendukung' => $berkas,
                'tanggal_pengajuan' => $diajukan,
                'keputusan' => $keputusan,
                'alasan' => $alasan,
                'id_admin' => $keputusan ? $admin?->id_pengguna : null,
                'tanggal_keputusan' => $keputusan ? $diajukan->copy()->addDay() : null,
            ]);

            if ($email === 'mitra@foodloop.test') {
                foreach (self::TITIK_CONTOH as [$nama, $alamat, $patokan, $lat, $lng]) {
                    TitikPengambilan::updateOrCreate(
                        ['id_mitra' => $mitra->id_mitra, 'nama_titik' => $nama],
                        ['alamat' => $alamat, 'patokan' => $patokan, 'latitude' => $lat, 'longitude' => $lng],
                    );
                }
            }
        }
    }

    /** PDF satu halaman yang sah, berisi keterangan bahwa ini dokumen dummy. */
    private function pdfContoh(string $namaUsaha): string
    {
        $teks = 'Contoh dokumen verifikasi (dummy): '.preg_replace('/[^A-Za-z0-9 .,&-]/', '', $namaUsaha);
        $isi = "BT /F1 16 Tf 60 760 Td ({$teks}) Tj 0 -28 Td /F1 12 Tf (Data pengembangan lokal FoodLoop, bukan dokumen asli.) Tj ET";

        $objek = [
            '<< /Type /Catalog /Pages 2 0 R >>',
            '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>',
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>',
            '<< /Length '.strlen($isi)." >>\nstream\n{$isi}\nendstream",
        ];

        $pdf = "%PDF-1.4\n";
        $posisi = [];
        foreach ($objek as $i => $badan) {
            $posisi[] = strlen($pdf);
            $pdf .= ($i + 1)." 0 obj\n{$badan}\nendobj\n";
        }

        $xref = strlen($pdf);
        $pdf .= "xref\n0 ".(count($objek) + 1)."\n0000000000 65535 f \n";
        foreach ($posisi as $p) {
            $pdf .= sprintf("%010d 00000 n \n", $p);
        }

        return $pdf."trailer\n<< /Size ".(count($objek) + 1)." /Root 1 0 R >>\nstartxref\n{$xref}\n%%EOF\n";
    }
}
