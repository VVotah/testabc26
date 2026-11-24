<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use App\Models\Peran;
use App\Models\VerifikasiMitra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Fondasi (FOOD-30): dashboard sesuai peran akun.
 *
 * Statistik: angka null = modul pemilik datanya belum dibuat (tampil "Segera hadir").
 * Jangan diisi angka contoh. Begitu modulnya ada, ganti null dengan hitungan asli.
 *
 * Panel: [judul, ikon, kosong, isi]. 'kosong' berisi judul dan kalimat untuk keadaan
 * kosong; untuk modul yang belum dibuat pakai $segera supaya pengguna tahu bagian itu
 * belum tersedia. 'isi' (opsional) = ['view' => ..., 'data' => [...], 'aksi' => [label, url]]
 * dan hanya ditampilkan bila datanya ada.
 */
class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $peran = $request->user()->peran?->nama_peran;

        $segera = fn (string $modul) => [
            'judul' => 'Segera hadir',
            'teks' => "Bagian ini muncul setelah modul {$modul} tersedia.",
        ];

        $isi = match ($peran) {
            Peran::ADMIN => [
                'judul' => 'Dashboard Admin',
                'statistik' => [
                    ['Menunggu verifikasi', DB::table('mitra')->where('status_mitra', 'Menunggu verifikasi')->count(), 'perisai'],
                    ['Mitra aktif', DB::table('mitra')->where('status_mitra', 'Terverifikasi')->count(), 'toko'],
                    ['Laporan terbuka', null, 'bendera'],
                    ['Listing aktif', null, 'kotak'],
                ],
                'panel' => [
                    ['Antrean verifikasi mitra', 'perisai', [
                        'judul' => 'Tidak ada mitra yang menunggu verifikasi',
                        'teks' => 'Pengajuan mitra baru akan muncul di sini untuk ditinjau.',
                    ], $this->panelAntreanVerifikasi()],
                    ['Aktivitas terbaru', 'lonceng', $segera('Moderasi & Kualitas')],
                ],
            ],
            Peran::MITRA => [
                'judul' => 'Dashboard Mitra',
                'statistik' => [
                    ['Listing aktif', null, 'kotak'],
                    ['Permintaan baru', null, 'masuk'],
                    ['Pickup hari ini', null, 'jam'],
                    ['Porsi terselamatkan', null, 'daun'],
                ],
                'panel' => [
                    ['Jadwal surplus rutin', 'kalender', $segera('Listing Donasi')],
                    ['Permintaan terbaru', 'masuk', $segera('Pencarian & Permintaan')],
                ],
            ],
            Peran::RELAWAN => [
                'judul' => 'Dashboard Relawan',
                'statistik' => [
                    ['Tugas tersedia', null, 'rute'],
                    ['Tugas berjalan', null, 'jam'],
                    ['Tugas selesai', null, 'centang'],
                    ['Porsi diantar', null, 'daun'],
                ],
                'panel' => [
                    ['Tugas tersedia', 'rute', $segera('Pickup & Logistik')],
                    ['Tugas saya', 'rute', $segera('Pickup & Logistik')],
                ],
            ],
            default => [
                'judul' => 'Dashboard Penerima',
                'statistik' => [
                    ['Permintaan menunggu', null, 'masuk'],
                    ['Pickup terjadwal', null, 'kalender'],
                    ['Mitra diikuti', null, 'hati'],
                    ['Porsi diterima', null, 'daun'],
                ],
                'panel' => [
                    ['Permintaan terbaru', 'masuk', $segera('Pencarian & Permintaan')],
                    ['Listing terbaru', 'kotak', $segera('Listing Donasi')],
                ],
            ],
        };

        $mitra = $peran === Peran::MITRA ? $request->user()->mitra()->with('verifikasiTerakhir')->first() : null;
        $statusMitra = $mitra?->status_mitra;
        // Alasan penolakan ditampilkan ke mitra (proses bisnis To-Be: mitra menerima informasi penolakan).
        $alasanDitolak = $statusMitra === Mitra::STATUS_DITOLAK ? $mitra->verifikasiTerakhir?->alasan : null;

        return view('dashboard.index', [...$isi, 'peran' => $peran, 'statusMitra' => $statusMitra, 'alasanDitolak' => $alasanDitolak]);
    }

    /** Tiga pengajuan mitra yang paling lama menunggu (mockup Dashboard Admin). */
    private function panelAntreanVerifikasi(): array
    {
        return [
            'view' => 'dashboard.panel-antrean-verifikasi',
            'data' => ['antrean' => VerifikasiMitra::with('mitra')->menunggu()->oldest('tanggal_pengajuan')->limit(3)->get()],
            'aksi' => ['Lihat semua', route('verifikasi-mitra.index')],
        ];
    }
}
