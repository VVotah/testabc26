<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel titik_pengambilan (ERD 4.3.1). Satu mitra dapat memiliki banyak titik.
     * Dirujuk modul lain lewat FK id_titik (listing, jadwal_pickup).
     */
    public function up(): void
    {
        Schema::create('titik_pengambilan', function (Blueprint $table) {
            $table->id('id_titik');
            $table->foreignId('id_mitra')->constrained('mitra', 'id_mitra');
            $table->string('nama_titik');
            $table->string('alamat');
            $table->string('patokan')->nullable();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('titik_pengambilan');
    }
};
