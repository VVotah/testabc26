<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artikel', function (Blueprint $table) {
            $table->id('id_artikel');
            $table->foreignId('id_penulis')
                ->constrained('pengguna', 'id_pengguna')
                ->restrictOnDelete();
            $table->string('judul', 150);
            $table->text('isi')->nullable(); // wajib saat diterbitkan (ASUMSI)
            $table->string('gambar', 255)->nullable();
            $table->enum('status_artikel', ['Draf', 'Terbit', 'Diarsipkan'])->default('Draf');
            $table->timestamp('waktu_dibuat')->useCurrent();
            $table->timestamp('waktu_terbit')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artikel');
    }
};