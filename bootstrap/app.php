<?php

use App\Http\Middleware\PastikanAkunAktif;
use App\Http\Middleware\PastikanMitraTerverifikasi;
use App\Http\Middleware\PastikanPeran;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Akun Ditangguhkan langsung dikeluarkan di setiap request web.
        $middleware->web(append: [
            PastikanAkunAktif::class,
        ]);

        // Pembatas akses per peran, contoh: ->middleware('peran:Admin').
        $middleware->alias([
            'peran' => PastikanPeran::class,
            // Hanya mitra Terverifikasi, contoh: ->middleware('mitra.terverifikasi').
            'mitra.terverifikasi' => PastikanMitraTerverifikasi::class,
        ]);

        // Belum login -> halaman masuk. Sudah login membuka halaman tamu -> dashboard.
        $middleware->redirectGuestsTo(fn () => route('login'));
        $middleware->redirectUsersTo(fn () => route('dashboard'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
