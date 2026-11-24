<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_pickup', function (Blueprint $table) {
            $table->id('id_jadwal_pickup');
            $table->foreignId('id_donatur')->constrained('pengguna', 'id_pengguna')->cascadeOnDelete();
            $table->foreignId('id_penerima')->constrained('pengguna', 'id_pengguna')->cascadeOnDelete();
            $table->unsignedBigInteger('id_permintaan')->nullable();
            $table->dateTime('slot_waktu');
            $table->foreignId('id_titik')->constrained('titik_pengambilan', 'id_titik')->cascadeOnDelete();
            $table->enum('status_jadwal', [
                'Dijadwalkan',
                'Dalam pengantaran',
                'Diserahkan',
                'Selesai',
                'Dibatalkan',
            ])->default('Dijadwalkan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_pickup');
    }
};