<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel klaim_porsi (ERD 4.3.5).
     * Dibuat dan dibatalkan di PBI-14, disetujui atau ditolak di PBI-15.
     */
    public function up(): void
    {
        Schema::create('klaim_porsi', function (Blueprint $table) {
            $table->id('id_klaim_porsi');
            $table->foreignId('id_postingan_sharing')
                ->constrained('postingan_sharing', 'id_postingan_sharing')
                ->cascadeOnDelete();
            $table->foreignId('id_pengklaim')->constrained('pengguna', 'id_pengguna');
            $table->unsignedInteger('jumlah_porsi');
            // Menunggu Persetujuan (default), Disetujui, Ditolak, Dibatalkan
            $table->string('status_klaim', 25)->default('Menunggu Persetujuan');
            $table->timestamp('waktu_pengajuan')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('klaim_porsi');
    }
};