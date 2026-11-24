<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ulasan', function (Blueprint $table) {
            $table->id('id_ulasan');

            $table->foreignId('id_penerima')
                ->constrained('pengguna', 'id_pengguna');

            $table->foreignId('id_donatur')
                ->constrained('pengguna', 'id_pengguna');

            $table->unsignedBigInteger('id_jadwal_pickup')->unique();

            $table->unsignedTinyInteger('rating');
            $table->text('teks_ulasan')->nullable();
            $table->timestamp('waktu_ulasan')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ulasan');
    }
};
