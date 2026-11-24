<?php

use App\Http\Controllers\Auth\DaftarController;
use App\Http\Controllers\Auth\DaftarMitraController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
| File bersama (milik Nug). Jangan menambah route modul di sini.
| Route tiap modul ditulis di routes/modul/<nama_modul>.php dan dimuat otomatis di bawah.
*/

Route::redirect('/', '/dashboard');

// Fondasi: masuk dan keluar (FOOD-32), daftar akun (FOOD-31).
Route::middleware('guest')->group(function () {
    Route::get('/masuk', [LoginController::class, 'form'])->name('login');
    Route::post('/masuk', [LoginController::class, 'masuk'])->name('login.proses');

    Route::get('/daftar', [DaftarController::class, 'form'])->name('register');
    Route::post('/daftar', [DaftarController::class, 'simpan'])->middleware('throttle:10,1')->name('register.simpan');

    Route::get('/daftar/mitra', [DaftarMitraController::class, 'form'])->name('register.mitra');
    Route::post('/daftar/mitra', [DaftarMitraController::class, 'simpan'])->middleware('throttle:10,1')->name('register.mitra.simpan');
});

Route::middleware('auth')->group(function () {
    Route::post('/keluar', [LoginController::class, 'keluar'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
});

// Katalog komponen tampilan, hanya untuk pengembangan lokal.
if (app()->environment('local')) {
    Route::view('/komponen', 'komponen')->middleware('auth')->name('komponen');
}

// Muat semua file route modul (urut abjad nama file).
foreach (glob(__DIR__.'/modul/*.php') as $fileRouteModul) {
    require $fileRouteModul;
}
