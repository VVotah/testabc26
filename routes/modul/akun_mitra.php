<?php

use App\Http\Controllers\AkunMitra\ProfilMitraController;
use App\Http\Controllers\AkunMitra\TitikPengambilanController;
use App\Http\Controllers\AkunMitra\VerifikasiMitraController;
use Illuminate\Support\Facades\Route;

/*
| Modul 1 Akun & Mitra (pemilik: Nug, PBI-01, 02, 03).
| Tulis semua route modul ini di sini. File ini dimuat otomatis oleh routes/web.php,
| jadi jangan menambah route di routes/web.php.
*/

// Fondasi (FOOD-33): verifikasi mitra oleh Admin.
Route::middleware(['auth', 'peran:Admin'])
    ->prefix('verifikasi-mitra')
    ->name('verifikasi-mitra.')
    ->group(function () {
        Route::get('/', [VerifikasiMitraController::class, 'index'])->name('index');
        Route::get('/{pengajuan}', [VerifikasiMitraController::class, 'show'])->whereNumber('pengajuan')->name('show');
        Route::get('/{pengajuan}/dokumen', [VerifikasiMitraController::class, 'dokumen'])->whereNumber('pengajuan')->name('dokumen');
        Route::post('/{pengajuan}/keputusan', [VerifikasiMitraController::class, 'putuskan'])->whereNumber('pengajuan')->name('putuskan');
    });

// PBI-03 (FOOD-11): Mitra/Donatur mengelola titik pengambilan miliknya.
// Sesuai use case 4.1.3, mitra yang belum terverifikasi tetap boleh mengelola titik.
Route::middleware(['auth', 'peran:Mitra/Donatur'])
    ->prefix('titik-pengambilan')
    ->name('titik-pengambilan.')
    ->group(function () {
        Route::get('/', [TitikPengambilanController::class, 'index'])->name('index');
        Route::get('/tambah', [TitikPengambilanController::class, 'create'])->name('create');
        Route::post('/', [TitikPengambilanController::class, 'store'])->name('store');
        Route::get('/{titik}', [TitikPengambilanController::class, 'show'])->whereNumber('titik')->name('show');
        Route::get('/{titik}/ubah', [TitikPengambilanController::class, 'edit'])->whereNumber('titik')->name('edit');
        Route::put('/{titik}', [TitikPengambilanController::class, 'update'])->whereNumber('titik')->name('update');
        Route::delete('/{titik}', [TitikPengambilanController::class, 'destroy'])->whereNumber('titik')->name('destroy');
    });

// PBI-02 (FOOD-10): Mitra/Donatur melihat dan memperbarui profil usahanya (semua status mitra).
Route::middleware(['auth', 'peran:Mitra/Donatur'])
    ->prefix('profil-mitra')
    ->name('profil-mitra.')
    ->group(function () {
        Route::get('/', [ProfilMitraController::class, 'edit'])->name('edit');
        Route::put('/', [ProfilMitraController::class, 'update'])->name('update');
        Route::post('/ajukan-ulang', [ProfilMitraController::class, 'ajukanUlang'])->middleware('throttle:10,1')->name('ajukan-ulang');
    });
