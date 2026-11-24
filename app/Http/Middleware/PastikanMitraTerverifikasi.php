<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Hanya mitra berstatus Terverifikasi yang boleh lewat (diagram kelas: Mitra::cekTerverifikasi).
 * Pasang SETELAH auth dan peran, contoh di route listing:
 *   Route::middleware(['auth', 'peran:Mitra/Donatur', 'mitra.terverifikasi'])->group(...);
 * Mitra yang belum terverifikasi dialihkan ke dashboard dengan pesan.
 */
class PastikanMitraTerverifikasi
{
    public function handle(Request $request, Closure $next): Response
    {
        $mitra = $request->user()?->mitra;

        if (! $mitra?->terverifikasi()) {
            return redirect()->route('dashboard')->with(
                'galat',
                'Fitur ini bisa dipakai setelah data usahamu diverifikasi Admin.'
            );
        }

        return $next($request);
    }
}
