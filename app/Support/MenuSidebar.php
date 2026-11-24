<?php

namespace App\Support;

use App\Models\Pengguna;
use App\Models\Peran;
use Illuminate\Support\Facades\Route;

/**
 * Daftar menu sidebar per peran (file bersama, milik Nug).
 *
 * Tiap item: [label, nama route, pola route aktif (teks atau daftar pola)].
 * Menu yang nama route-nya belum terdaftar tampil nonaktif, dan otomatis aktif
 * begitu modul pemiliknya mendaftarkan route dengan nama tersebut.
 * Mau ganti nama route modulmu? Kabari Nug, jangan edit file ini sendiri.
 */
class MenuSidebar
{
    private const MENU = [
        Peran::ADMIN => [
            ['Dashboard', 'dashboard'],
            ['Verifikasi Mitra', 'verifikasi-mitra.index', 'verifikasi-mitra.*'],
            ['Laporan Masuk', 'laporan-masalah.index', 'laporan-masalah.*'],
            ['Artikel Edukasi', 'artikel.kelola', 'artikel.*'],
            ['Kampanye & Event', 'kampanye.kelola', 'kampanye.*'],
            ['Pengguna', 'pengguna.index', 'pengguna.*'],
        ],
        Peran::MITRA => [
            ['Dashboard', 'dashboard'],
            ['Jadwal Surplus', 'listing.jadwal-surplus.index', 'listing.jadwal-surplus.*'],
            // Route jadwal surplus juga berawalan "listing.", jadi pola Listing Makanan disebut satu per satu.
            ['Listing Makanan', 'listing.index', [
                'listing.index', 'listing.create', 'listing.store', 'listing.show',
                'listing.edit', 'listing.update', 'listing.close', 'listing.destroy',
            ]],
            ['Permintaan Masuk', 'permintaan.masuk', 'permintaan.*'],
            ['Pickup', 'pickup.index', 'pickup.*'],
            ['Titik Pengambilan', 'titik-pengambilan.index', 'titik-pengambilan.*'],
            ['Laporan Dampak', 'laporan-dampak.index', 'laporan-dampak.*'],
            ['Profil Mitra', 'profil-mitra.edit', 'profil-mitra.*'],
        ],
        Peran::PENERIMA => [
            ['Dashboard', 'dashboard'],
            ['Cari Makanan', 'cari.index', 'cari.*'],
            ['Peta Sebaran', 'peta.index', 'peta.*'],
            ['Permintaan Saya', 'permintaan.index', 'permintaan.*'],
            ['Pickup Saya', 'pickup.index', 'pickup.*'],
            ['Laporan Dampak', 'laporan-dampak.index', 'laporan-dampak.*'],
        ],
        Peran::RELAWAN => [
            ['Dashboard', 'dashboard'],
            ['Tugas Pengantaran', 'tugas-pengantaran.index', 'tugas-pengantaran.*'],
            ['Laporan Dampak', 'laporan-dampak.index', 'laporan-dampak.*'],
        ],
    ];

    // Tampil untuk semua peran, di bawah menu peran.
    private const MENU_UMUM = [
        ['Forum Sharing', 'forum.index', 'forum.*'],
        ['Edukasi', 'edukasi.index', 'edukasi.*'],
        ['Kampanye', 'kampanye-pengguna.index', 'kampanye-pengguna.*'],
    ];

    /**
     * @return list<array{label: string, url: ?string, aktif: bool}>
     */
    public static function untuk(Pengguna $pengguna): array
    {
        $menu = array_merge(self::MENU[$pengguna->peran?->nama_peran] ?? [], self::MENU_UMUM);

        return array_map(function (array $item) {
            [$label, $namaRoute] = $item;
            $pola = $item[2] ?? $namaRoute;
            $tersedia = Route::has($namaRoute);

            return [
                'label' => $label,
                'url' => $tersedia ? route($namaRoute) : null,
                'aktif' => $tersedia && request()->routeIs(...(array) $pola),
            ];
        }, $menu);
    }
}
