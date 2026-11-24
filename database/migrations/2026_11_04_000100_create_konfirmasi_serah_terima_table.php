<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('konfirmasi_serah_terima', function (Blueprint $table) {
            $table->id('id_konfirmasi');
            $table->foreignId('id_jadwal_pickup')
                ->constrained('jadwal_pickup', 'id_jadwal_pickup')
                ->cascadeOnDelete();
            $table->string('kode_pickup', 32)->unique();
            $table->enum('status_serah_terima', ['Menunggu', 'Berhasil', 'Gagal'])
                ->default('Menunggu');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('konfirmasi_serah_terima');
    }
};