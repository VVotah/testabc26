<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel verifikasi_mitra (ERD 4.3.1). Satu mitra dapat memiliki beberapa
     * pengajuan sebagai riwayat verifikasi.
     */
    public function up(): void
    {
        Schema::create('verifikasi_mitra', function (Blueprint $table) {
            $table->id('id_verifikasi');
            $table->foreignId('id_mitra')->constrained('mitra', 'id_mitra');
            $table->string('bukti_pendukung'); // lokasi berkas yang diunggah
            $table->dateTime('tanggal_pengajuan');
            // Kosong selama masih Menunggu verifikasi.
            $table->enum('keputusan', ['Terverifikasi', 'Ditolak'])->nullable();
            $table->string('alasan')->nullable(); // wajib jika ditolak
            // Admin yang memutuskan; kosong sebelum diputuskan.
            $table->foreignId('id_admin')->nullable()->constrained('pengguna', 'id_pengguna');
            $table->dateTime('tanggal_keputusan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verifikasi_mitra');
    }
};
