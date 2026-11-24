<?php

use App\Http\Controllers\Pickup\JadwalPickupController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {
    // PBI-10: Penjadwalan Pickup (yang mengajukan jadwal hanya Penerima, UC 4.1.10)
    Route::get('/pickup', [JadwalPickupController::class, 'index'])->name('pickup.index');
    Route::post('/pickup', [JadwalPickupController::class, 'store'])
        ->middleware('peran:Penerima')
        ->name('pickup.store');
    Route::patch('/pickup/{jadwal}/setujui', [JadwalPickupController::class, 'setujui'])
        ->middleware('peran:Mitra/Donatur')
        ->name('pickup.setujui');
    Route::patch('/pickup/{jadwal}/batalkan', [JadwalPickupController::class, 'batalkan'])
        ->name('pickup.batalkan');

    // PBI-12: Konfirmasi Kode Pickup (khusus Mitra/Donatur)
    Route::middleware(['peran:Mitra/Donatur'])->group(function () {
        Route::get('/pickup/konfirmasi', [JadwalPickupController::class, 'konfirmasi'])->name('pickup.konfirmasi');
        Route::post('/pickup/konfirmasi', [JadwalPickupController::class, 'storeKonfirmasi'])
            ->middleware('throttle:10,1')
            ->name('pickup.konfirmasi.store');
    });
});