
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listing', function (Blueprint $table) {
            $table->id('id_listing');

            $table->foreignId('id_mitra')
                ->constrained('mitra', 'id_mitra');

            $table->foreignId('id_titik')
                ->constrained('titik_pengambilan', 'id_titik');

            $table->foreignId('id_kategori_makanan')
                ->constrained('kategori_makanan', 'id_kategori_makanan');

            // Ditambahkan karena mockup form dan daftar menampilkan nama makanan.
            $table->string('nama_makanan', 100);

            $table->string('foto');
            $table->unsignedInteger('jumlah_porsi');

            $table->enum('tipe_listing', ['Gratis', 'Diskon']);
            $table->decimal('harga', 10, 2)->nullable();

            $table->dateTime('batas_kedaluwarsa');

            $table->enum('status_listing', [
                'Aktif',
                'Ditutup',
                'Habis',
                'Kedaluwarsa',
                'Ditangguhkan',
            ])->default('Aktif');

            $table->timestamps();

            $table->index(['status_listing', 'batas_kedaluwarsa']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listing');
    }
};
