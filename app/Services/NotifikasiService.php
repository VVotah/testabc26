<?php

namespace App\Services;

use App\Models\Notifikasi;

class NotifikasiService
{
    /**
     * Notifikasi kepada Donatur ketika ada permintaan baru.
     */
    public function permintaanBaru(
        int $idDonatur,
        int $idPermintaan
    ): Notifikasi {
        return Notifikasi::create([
            'id_pengguna' => $idDonatur,
            'jenis_perubahan' => 'Permintaan Baru',
            'id_objek' => $idPermintaan,
            'isi_notifikasi' => 'Ada permintaan baru untuk listing makanan kamu.',
        ]);
    }

    /**
     * Membuat notifikasi ketika status permintaan berubah.
     */
    public function statusPermintaanBerubah(
        int $idPengguna,
        int $idPermintaan,
        string $statusBaru
    ): Notifikasi {
        return Notifikasi::create([
            'id_pengguna' => $idPengguna,
            'jenis_perubahan' => 'Status Permintaan',
            'id_objek' => $idPermintaan,
            'isi_notifikasi' => 'Status permintaan makanan kamu telah berubah menjadi ' . $statusBaru . '.',
        ]);
    }

    /**
     * Membuat notifikasi ketika status pickup berubah.
     */
    public function statusPickupBerubah(
        int $idPengguna,
        int $idPickup,
        string $statusBaru
    ): Notifikasi {
        return Notifikasi::create([
            'id_pengguna' => $idPengguna,
            'jenis_perubahan' => 'Status Pickup',
            'id_objek' => $idPickup,
            'isi_notifikasi' => 'Status pickup makanan kamu telah berubah menjadi ' . $statusBaru . '.',
        ]);
    }
}
