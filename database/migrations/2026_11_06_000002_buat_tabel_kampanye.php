<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kampanye', function (Blueprint $table) {
            $table->id('id_kampanye');
            $table->foreignId('id_pembuat')
                ->constrained('pengguna', 'id_pengguna')
                ->restrictOnDelete();
            $table->string('nama_kampanye', 150);
            $table->text('deskripsi');
            $table->string('lokasi', 255);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->enum('status_kampanye', ['Akan Datang', 'Berlangsung', 'Selesai', 'Dibatalkan'])
                ->default('Akan Datang');
            $table->timestamp('waktu_dibuat')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kampanye');
    }
};