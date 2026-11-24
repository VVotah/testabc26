<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Fondasi (FOOD-32): masuk dan keluar.
 */
class LoginController extends Controller
{
    private const MAKS_PERCOBAAN = 5;

    public function form(): View
    {
        return view('auth.login');
    }

    public function masuk(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'kata_sandi' => ['required', 'string'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'kata_sandi.required' => 'Kata sandi wajib diisi.',
        ]);

        $kunciBatas = Str::lower($data['email']).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($kunciBatas, self::MAKS_PERCOBAAN)) {
            throw ValidationException::withMessages([
                'email' => 'Terlalu banyak percobaan masuk. Coba lagi dalam '
                    .RateLimiter::availableIn($kunciBatas).' detik.',
            ]);
        }

        // Kunci 'password' wajib bernama begitu; Laravel mencocokkannya dengan kolom kata_sandi.
        $kredensial = ['email' => $data['email'], 'password' => $data['kata_sandi']];

        if (! Auth::validate($kredensial)) {
            RateLimiter::hit($kunciBatas);

            throw ValidationException::withMessages([
                'email' => 'Email atau kata sandi salah.',
            ]);
        }

        // Kredensial benar, tetapi akun Ditangguhkan tidak boleh masuk.
        $pengguna = Pengguna::where('email', $data['email'])->first();

        if (! $pengguna->aktif()) {
            throw ValidationException::withMessages([
                'email' => 'Akun kamu ditangguhkan. Hubungi Admin FoodLoop.',
            ]);
        }

        Auth::login($pengguna, $request->boolean('ingat_saya'));
        RateLimiter::clear($kunciBatas);
        $request->session()->regenerate(); // cegah session fixation

        return redirect()->intended(route('dashboard'));
    }

    public function keluar(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('sukses', 'Kamu sudah keluar dari FoodLoop.');
    }
}
