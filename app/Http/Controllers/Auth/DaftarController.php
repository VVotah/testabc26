<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use App\Models\Peran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Fondasi (FOOD-31): daftar akun Penerima atau Relawan.
 * Mitra mendaftar lewat DaftarMitraController karena butuh data usaha dan dokumen.
 */
class DaftarController extends Controller
{
    /**
     * Pilihan peran yang boleh dipilih sendiri di form. Form hanya mengirim kuncinya;
     * id_peran dicari server dari nama peran, tidak pernah diterima dari form.
     * Admin dan Mitra/Donatur sengaja tidak ada di sini.
     */
    public const PILIHAN_PERAN = [
        'penerima' => Peran::PENERIMA,
        'relawan' => Peran::RELAWAN,
    ];

    public function form(): View
    {
        return view('auth.daftar');
    }

    public function simpan(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', Rule::unique('pengguna', 'email')],
            'kata_sandi' => ['required', 'string', 'min:8', 'max:72'],
            'peran' => ['required', Rule::in(array_keys(self::PILIHAN_PERAN))],
            'setuju' => ['accepted'],
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar. Silakan masuk.',
            'kata_sandi.required' => 'Kata sandi wajib diisi.',
            'kata_sandi.min' => 'Kata sandi minimal 8 karakter.',
            'peran.required' => 'Pilih daftar sebagai Penerima atau Relawan.',
            'peran.in' => 'Pilih daftar sebagai Penerima atau Relawan.',
            'setuju.accepted' => 'Kamu perlu menyetujui syarat dan ketentuan.',
        ]);

        $pengguna = Pengguna::create([
            'id_peran' => Peran::where('nama_peran', self::PILIHAN_PERAN[$data['peran']])->value('id_peran'),
            'nama' => $data['nama'],
            'email' => $data['email'],
            'kata_sandi' => $data['kata_sandi'], // di-hash otomatis oleh model (cast 'hashed')
            'status_akun' => Pengguna::STATUS_AKTIF,
        ]);

        Auth::login($pengguna);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('sukses', 'Akun berhasil dibuat. Selamat datang di FoodLoop!');
    }
}
