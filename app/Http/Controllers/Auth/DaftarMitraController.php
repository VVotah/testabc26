<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Mitra;
use App\Models\Pengguna;
use App\Models\Peran;
use App\Models\VerifikasiMitra;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

/**
 * Fondasi (FOOD-31): daftar sebagai mitra (mockup SCR-00.6, proposal 4.5.6).
 * Menyimpan pengguna (peran Mitra/Donatur), mitra (Menunggu verifikasi), dan
 * pengajuan verifikasi_mitra beserta dokumen bukti dalam satu transaksi.
 */
class DaftarMitraController extends Controller
{
    /** Disk privat (storage/app/private): dokumen hanya boleh dibuka Admin (NFR-03). */
    public const DISK_DOKUMEN = 'local';

    public const FOLDER_DOKUMEN = 'verifikasi_mitra';

    public function form(): View
    {
        return view('auth.daftar-mitra', ['jenisMitra' => Mitra::JENIS]);
    }

    public function simpan(Request $request): RedirectResponse
    {
        $data = $request->validate([
            ...Mitra::aturanDataUsaha(),
            'nama' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', Rule::unique('pengguna', 'email')],
            'kata_sandi' => ['required', 'string', 'min:8', 'max:72'],
            ...Mitra::aturanDokumen(),
            'setuju' => ['accepted'],
        ], [
            ...Mitra::pesanDataUsaha(),
            ...Mitra::pesanDokumen(),
            'nama.required' => 'Nama penanggung jawab wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar. Silakan masuk.',
            'kata_sandi.required' => 'Kata sandi wajib diisi.',
            'kata_sandi.min' => 'Kata sandi minimal 8 karakter.',
            'setuju.accepted' => 'Kamu perlu menyetujui syarat dan ketentuan.',
        ]);

        $lokasiBerkas = $request->file('bukti_pendukung')->store(self::FOLDER_DOKUMEN, self::DISK_DOKUMEN);

        try {
            $pengguna = DB::transaction(function () use ($data, $lokasiBerkas) {
                $pengguna = Pengguna::create([
                    // Peran ditetapkan server, tidak dari form.
                    'id_peran' => Peran::where('nama_peran', Peran::MITRA)->value('id_peran'),
                    'nama' => $data['nama'],
                    'email' => $data['email'],
                    'kata_sandi' => $data['kata_sandi'], // di-hash otomatis oleh model (cast 'hashed')
                    'status_akun' => Pengguna::STATUS_AKTIF,
                ]);

                $mitra = Mitra::create([
                    'id_pengguna' => $pengguna->id_pengguna,
                    'nama_mitra' => $data['nama_mitra'],
                    'jenis_mitra' => $data['jenis_mitra'],
                    'alamat' => Mitra::gabungAlamat($data['alamat_jalan'], $data['kota'], $data['kode_pos']),
                    'status_mitra' => Mitra::STATUS_MENUNGGU,
                ]);

                VerifikasiMitra::create([
                    'id_mitra' => $mitra->id_mitra,
                    'bukti_pendukung' => $lokasiBerkas,
                    'tanggal_pengajuan' => now(),
                ]);

                return $pengguna;
            });
        } catch (Throwable $galat) {
            // Data gagal disimpan: buang berkas supaya tidak tertinggal tanpa pemilik.
            Storage::disk(self::DISK_DOKUMEN)->delete($lokasiBerkas);

            throw $galat;
        }

        Auth::login($pengguna);
        $request->session()->regenerate();

        return redirect()->route('dashboard')
            ->with('sukses', 'Pengajuan mitra berhasil dikirim. Admin akan memverifikasi dalam 1-3 hari kerja.');
    }
}
