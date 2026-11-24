<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel komentar (ERD 4.3.5), koordinasi pengambilan makanan (PBI-14).
     */
    public function up(): void
    {
        Schema::create('komentar', function (Blueprint $table) {
            $table->id('id_komentar');
            $table->foreignId('id_postingan_sharing')
                ->constrained('postingan_sharing', 'id_postingan_sharing')
                ->cascadeOnDelete();
            $table->foreignId('id_penulis')->constrained('pengguna', 'id_pengguna');
            $table->text('isi_komentar');
            $table->timestamp('waktu_komentar')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('komentar');
    }
};