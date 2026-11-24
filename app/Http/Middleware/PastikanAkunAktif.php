<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Akun berstatus Ditangguhkan tidak boleh memakai aplikasi.
 * Jika akun yang sedang login ditangguhkan, sesi diakhiri saat itu juga.
 */
class PastikanAkunAktif
{
    public function handle(Request $request, Closure $next): Response
    {
        $pengguna = Auth::user();

        if ($pengguna && ! $pengguna->aktif()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Akun kamu ditangguhkan. Hubungi Admin FoodLoop.',
            ]);
        }

        return $next($request);
    }
}
