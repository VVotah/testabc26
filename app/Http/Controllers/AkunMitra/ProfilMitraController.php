<?php

namespace App\Http\Controllers\AkunMitra;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\DaftarMitraController;
use App\Models\Mitra;
use App\Models\VerifikasiMitra;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

/**
 * PBI-02 (FOOD-10): Mitra/Donatur melihat dan memperbarui profil usahanya.
 * Use case 4.1.2, FR-02, mockup 4.5.9. Selalu memakai data mitra yang sedang login
 * (tidak ada id di URL). Mengubah profil tidak mengubah status dan tidak memicu verifikasi ulang.
 *
 * Mitra yang ditolak bisa memperbaiki data di sini lalu mengajukan verifikasi ulang lewat
 * tombol terpisah (ajukanUlang). Dokumen baru opsional; bila kosong, dokumen terakhir dipakai lagi.
 */
class ProfilMitraController extends Controller
{
    /** lihatProfilMitra() */
    public function edit(Request $request): View
    {
        $mitra = $this->mitra($request)->load('verifikasiTerakhir');

        return view('akun_mitra.profil.edit', [
            'mitra' => $mitra,
            'jenisMitra' => Mitra::JENIS,
        ]);
    }

    /** ubahProfilMitra() */
    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate(Mitra::aturanDataUsaha(), Mitra::pesanDataUsaha());

        $this->mitra($request)->ubahProfilMitra($data);

        return redirect()->route('profil-mitra.edit')->with('sukses', 'Profil usaha berhasil diperbarui.');
    }

    /** Pengajuan verifikasi ulang setelah ditolak (buatPengajuan). */
    public function ajukanUlang(Request $request): RedirectResponse
    {
        $mitra = $this->mitra($request)->load('verifikasiTerakhir');

        if ($mitra->status_mitra !== Mitra::STATUS_DITOLAK) {
            return redirect()->route('profil-mitra.edit')
                ->with('galat', 'Pengajuan ulang hanya bisa dilakukan setelah pengajuan sebelumnya ditolak.');
        }

        $request->validate(Mitra::aturanDokumen(wajib: false), Mitra::pesanDokumen());

        $berkasBaru = $request->file('bukti_pendukung')
            ?->store(DaftarMitraController::FOLDER_DOKUMEN, DaftarMitraController::DISK_DOKUMEN);

        $berkas = $berkasBaru ?? $mitra->verifikasiTerakhir?->bukti_pendukung;

        if (! $berkas) {
            return back()->withErrors(['bukti_pendukung' => 'Unggah dokumen verifikasi (NIB atau KTP penanggung jawab).']);
        }

        try {
            VerifikasiMitra::buatPengajuan($mitra, $berkas);
        } catch (Throwable $galat) {
            if ($berkasBaru) {
                Storage::disk(DaftarMitraController::DISK_DOKUMEN)->delete($berkasBaru);
            }

            throw $galat;
        }

        return redirect()->route('profil-mitra.edit')
            ->with('sukses', 'Pengajuan verifikasi ulang terkirim. Admin akan meninjaunya dalam 1-3 hari kerja.');
    }

    /** Data usaha mitra yang sedang login. */
    private function mitra(Request $request): Mitra
    {
        // Selalu baca dari database supaya status mitra yang dipakai adalah yang terbaru.
        $mitra = $request->user()->mitra()->first();

        abort_unless($mitra, 403, 'Akun ini belum punya data usaha mitra.');

        return $mitra;
    }
}
