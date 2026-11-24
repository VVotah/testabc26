<?php

use App\Http\Controllers\ForumSharing\PostinganSharingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ForumSharing\KlaimPorsiController;
use App\Http\Controllers\ForumSharing\KomentarController;

Route::middleware('auth')->prefix('forum-sharing')->name('forum.')->group(function () {
    Route::get('/', [PostinganSharingController::class, 'index'])->name('index');
    Route::get('/buat', [PostinganSharingController::class, 'create'])->name('create');
    Route::post('/', [PostinganSharingController::class, 'store'])->name('store');
    Route::get('/{postingan}', [PostinganSharingController::class, 'show'])->name('show');
    Route::get('/{postingan}/ubah', [PostinganSharingController::class, 'edit'])->name('edit');
    Route::put('/{postingan}', [PostinganSharingController::class, 'update'])->name('update');
    Route::patch('/{postingan}/tutup', [PostinganSharingController::class, 'tutup'])->name('tutup');
    Route::delete('/{postingan}', [PostinganSharingController::class, 'destroy'])->name('destroy');
    // PBI-14: Klaim porsi
    Route::post('/{postingan}/klaim', [KlaimPorsiController::class, 'store'])->name('klaim.store');
    Route::delete('/{postingan}/klaim/{klaimPorsi}', [KlaimPorsiController::class, 'destroy'])->name('klaim.destroy');
    // PBI-14: Komentar
    Route::post('/{postingan}/komentar', [KomentarController::class, 'store'])->name('komentar.store');
    Route::delete('/{postingan}/komentar/{komentar}', [KomentarController::class, 'destroy'])->name('komentar.destroy');
});
