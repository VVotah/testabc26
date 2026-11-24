<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel pengguna (ERD 4.3.1). Harus dijalankan setelah tabel peran.
     */
    public function up(): void
    {
        Schema::create('pengguna', function (Blueprint $table) {
            $table->id('id_pengguna');
            $table->foreignId('id_peran')->constrained('peran', 'id_peran');
            $table->string('nama');
            $table->string('email')->unique();
            $table->string('kata_sandi'); // disimpan dalam bentuk hash
            $table->enum('status_akun', ['Aktif', 'Ditangguhkan'])->default('Aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengguna');
    }
};
