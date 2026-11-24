<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Batasi halaman untuk peran tertentu. Pasang SETELAH middleware auth.
 *
 * Contoh di file route modul:
 *   Route::middleware(['auth', 'peran:Admin'])->group(...);
 *   Route::middleware(['auth', 'peran:Mitra/Donatur'])->group(...);
 *   Route::middleware(['auth', 'peran:Penerima,Relawan'])->group(...);   // salah satu
 *
 * Nama peran harus sama persis dengan konstanta di App\Models\Peran.
 */
class PastikanPeran
{
    public function handle(Request $request, Closure $next, string ...$peran): Response
    {
        $pengguna = $request->user();

        if (! $pengguna || ! $pengguna->punyaPeran(...$peran)) {
            abort(403, 'Halaman ini tidak tersedia untuk peran akunmu.');
        }

        return $next($request);
    }
}
