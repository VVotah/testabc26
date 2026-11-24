<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_surplus', function (Blueprint $table) {
            $table->id('id_jadwal_surplus');

            $table->foreignId('id_mitra')
                ->constrained('mitra', 'id_mitra')
                ->cascadeOnDelete();

            $table->string('hari', 20);
            $table->time('jam');
            $table->unsignedInteger('estimasi_porsi');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_surplus');
    }
};
