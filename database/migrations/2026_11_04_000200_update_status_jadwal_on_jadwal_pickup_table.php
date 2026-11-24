<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwal_pickup', function (Blueprint $table) {
            $table->enum('status_jadwal', [
                'Menunggu',
                'Disetujui',
                'Dijadwalkan',
                'Dalam pengantaran',
                'Diserahkan',
                'Selesai',
                'Dibatalkan',
            ])->default('Menunggu')->change();
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_pickup', function (Blueprint $table) {
            $table->enum('status_jadwal', [
                'Dijadwalkan',
                'Dalam pengantaran',
                'Diserahkan',
                'Selesai',
                'Dibatalkan',
            ])->default('Dijadwalkan')->change();
        });
    }
};