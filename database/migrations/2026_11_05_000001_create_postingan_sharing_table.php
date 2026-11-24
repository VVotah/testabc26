<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('postingan_sharing', function (Blueprint $table) {
            $table->id('id_postingan_sharing');
            $table->foreignId('id_pengunggah')->constrained('pengguna', 'id_pengguna');
            $table->string('nama_makanan', 150);
            $table->string('foto_makanan', 255);
            $table->unsignedInteger('sisa_porsi');
            $table->string('lokasi_pengambilan', 255);
            $table->dateTime('batas_waktu');
            $table->string('status_postingan', 20)->default('Aktif');
            $table->timestamp('waktu_dibuat')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('postingan_sharing');
    }
};