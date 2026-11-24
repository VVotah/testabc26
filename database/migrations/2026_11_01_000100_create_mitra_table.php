<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel mitra (ERD 4.3.1). Satu akun pengguna hanya memiliki satu data mitra.
     */
    public function up(): void
    {
        Schema::create('mitra', function (Blueprint $table) {
            $table->id('id_mitra');
            $table->foreignId('id_pengguna')->unique()->constrained('pengguna', 'id_pengguna');
            $table->string('nama_mitra');
            $table->string('jenis_mitra'); // mis. Kantin, Restoran, Toko roti, Katering, Panitia acara
            $table->string('alamat'); // alamat lengkap beserta kota dan kode pos
            $table->enum('status_mitra', ['Menunggu verifikasi', 'Terverifikasi', 'Ditolak', 'Ditangguhkan'])
                ->default('Menunggu verifikasi');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mitra');
    }
};
