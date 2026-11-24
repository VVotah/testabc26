<?php

use App\Http\Controllers\ModerasiKualitas\AdminLaporanMasalahController;
use App\Http\Controllers\ModerasiKualitas\LaporanMasalahController;
use App\Http\Controllers\ModerasiKualitas\NotifikasiController;
use App\Http\Controllers\ModerasiKualitas\UlasanController;
use Illuminate\Support\Facades\Route;

// PBI-19: Pelaporan Masalah oleh Pengguna
Route::middleware('auth')->group(function () {
    Route::post('/laporan-masalah', [LaporanMasalahController::class, 'store'])
        ->name('laporan-masalah.store');
});

// PBI-19: Pengelolaan Laporan oleh Admin
Route::middleware(['auth', 'peran:Admin'])
    ->prefix('laporan-masalah')
    ->name('laporan-masalah.')
    ->group(function () {
        Route::get('/', [AdminLaporanMasalahController::class, 'index'])
            ->name('index');

        Route::get('/{id}', [AdminLaporanMasalahController::class, 'show'])
            ->whereNumber('id')
            ->name('show');

        Route::post('/{id}/tindak-lanjut', [AdminLaporanMasalahController::class, 'tindakLanjuti'])
            ->whereNumber('id')
            ->name('tindak-lanjuti');
    });

// PBI-21: Notifikasi
Route::middleware('auth')->group(function () {
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])
        ->name('notifikasi.index');

    Route::get('/notifikasi/{id}', [NotifikasiController::class, 'show'])
        ->whereNumber('id')
        ->name('notifikasi.show');

    Route::delete('/notifikasi/{id}', [NotifikasiController::class, 'destroy'])
        ->whereNumber('id')
        ->name('notifikasi.destroy');

    // PBI-20: Ulasan
    Route::get('/ulasan', [UlasanController::class, 'index'])
        ->name('ulasan.index');

    Route::get('/ulasan/{id}/create', [UlasanController::class, 'create'])
        ->whereNumber('id')
        ->name('ulasan.create');

    Route::post('/ulasan/{id}', [UlasanController::class, 'store'])
        ->whereNumber('id')
        ->name('ulasan.store');
});
