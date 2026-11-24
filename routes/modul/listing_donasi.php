<?php

use App\Http\Controllers\Listing\ListingController;
use App\Http\Controllers\Listing\JadwalSurplusController;
use Illuminate\Support\Facades\Route;

/*
| Modul 2 Listing Donasi (pemilik: Ica, PBI-04, 05, 06).
| File ini dimuat otomatis oleh routes/web.php.
*/

Route::middleware(['auth', 'peran:Mitra/Donatur', 'mitra.terverifikasi'])
    ->prefix('listing-makanan')
    ->name('listing.')
    ->group(function () {
        // Menampilkan daftar listing milik mitra.
        Route::get('/', [ListingController::class, 'index'])
            ->name('index');

        // Menampilkan form tambah listing.
        Route::get('/tambah', [ListingController::class, 'create'])
            ->name('create');

        // Menyimpan listing baru.
        Route::post('/', [ListingController::class, 'store'])
            ->name('store');

                // Menampilkan daftar jadwal surplus milik mitra.
        Route::get('/jadwal-surplus', [JadwalSurplusController::class, 'index'])
            ->name('jadwal-surplus.index');

        // Menampilkan form tambah jadwal surplus.
        Route::get('/jadwal-surplus/tambah', [JadwalSurplusController::class, 'create'])
            ->name('jadwal-surplus.create');

        // Menyimpan jadwal surplus baru.
        Route::post('/jadwal-surplus', [JadwalSurplusController::class, 'store'])
            ->name('jadwal-surplus.store');

        // Menampilkan form edit jadwal surplus.
        Route::get('/jadwal-surplus/{jadwalSurplus}/edit', [JadwalSurplusController::class, 'edit'])
            ->name('jadwal-surplus.edit');

        // Memperbarui jadwal surplus.
        Route::put('/jadwal-surplus/{jadwalSurplus}', [JadwalSurplusController::class, 'update'])
            ->name('jadwal-surplus.update');

        // Menghapus jadwal surplus.
        Route::delete('/jadwal-surplus/{jadwalSurplus}', [JadwalSurplusController::class, 'destroy'])
            ->name('jadwal-surplus.destroy');

        // Menampilkan detail listing milik mitra.
        Route::get('/{listing}', [ListingController::class, 'show'])
            ->name('show');

        // Menampilkan form edit listing.
        Route::get('/{listing}/edit', [ListingController::class, 'edit'])
            ->name('edit');

        // Memperbarui listing.
        Route::put('/{listing}', [ListingController::class, 'update'])
            ->name('update');

        // Menutup listing milik mitra.
        Route::post('/{listing}/tutup', [ListingController::class, 'close'])
            ->name('close');

        // Menghapus listing milik mitra.
        Route::delete('/{listing}', [ListingController::class, 'destroy'])
            ->name('destroy');
    });

// Katalog listing untuk penerima.
Route::middleware(['auth', 'peran:Penerima'])
    ->prefix('cari-makanan')
    ->name('cari.')
    ->group(function () {
        Route::get('/', [ListingController::class, 'katalog'])
            ->name('index');
    });
