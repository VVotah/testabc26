<?php

use App\Http\Controllers\EdukasiDampak\ArtikelController;
use App\Http\Controllers\EdukasiDampak\DaftarKampanyeController;
use App\Http\Controllers\EdukasiDampak\EdukasiController;
use App\Http\Controllers\EdukasiDampak\KampanyeController;
use Illuminate\Support\Facades\Route;

/*
| Modul 6 Edukasi & Dampak (pemilik: Rhein, PBI-16, 17, 18).
| Tulis semua route modul ini di sini. File ini dimuat otomatis oleh routes/web.php,
| jadi jangan menambah route di routes/web.php.
| Nama route daftar kelola (artikel.kelola, kampanye.kelola) mengikuti MenuSidebar milik Nug.
*/

// PBI-16: Admin kelola artikel edukasi (tanpa hapus, hanya arsipkan)
Route::middleware(['auth', 'peran:Admin'])->prefix('kelola-artikel')->name('artikel.')->group(function () {
    Route::get('/', [ArtikelController::class, 'index'])->name('kelola');
    Route::get('/buat', [ArtikelController::class, 'create'])->name('create');
    Route::post('/', [ArtikelController::class, 'store'])->name('store');
    Route::get('/{artikel}/ubah', [ArtikelController::class, 'edit'])->name('edit');
    Route::put('/{artikel}', [ArtikelController::class, 'update'])->name('update');
    Route::patch('/{artikel}/terbitkan', [ArtikelController::class, 'terbitkan'])->name('terbitkan');
    Route::patch('/{artikel}/arsipkan', [ArtikelController::class, 'arsipkan'])->name('arsipkan');
});

// PBI-16: Pengguna membaca artikel berstatus Terbit
Route::middleware('auth')->prefix('edukasi')->name('edukasi.')->group(function () {
    Route::get('/', [EdukasiController::class, 'index'])->name('index');
    Route::get('/{artikel}', [EdukasiController::class, 'show'])->name('show');
});

// PBI-17: Admin kelola kampanye/event (tanpa hapus, hanya batalkan)
Route::middleware(['auth', 'peran:Admin'])->prefix('kelola-kampanye')->name('kampanye.')->group(function () {
    Route::get('/', [KampanyeController::class, 'index'])->name('kelola');
    Route::get('/buat', [KampanyeController::class, 'create'])->name('create');
    Route::post('/', [KampanyeController::class, 'store'])->name('store');
    Route::get('/{kampanye}/ubah', [KampanyeController::class, 'edit'])->name('edit');
    Route::put('/{kampanye}', [KampanyeController::class, 'update'])->name('update');
    Route::patch('/{kampanye}/batalkan', [KampanyeController::class, 'batalkan'])->name('batalkan');
});

// PBI-17: Pengguna melihat kampanye yang belum selesai
Route::middleware('auth')->get('/kampanye', [DaftarKampanyeController::class, 'index'])
    ->name('kampanye-pengguna.index');