<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel peran (ERD 4.3.1): Admin, Mitra/Donatur, Penerima, Relawan.
     */
    public function up(): void
    {
        Schema::create('peran', function (Blueprint $table) {
            $table->id('id_peran');
            $table->string('nama_peran');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peran');
    }
};
