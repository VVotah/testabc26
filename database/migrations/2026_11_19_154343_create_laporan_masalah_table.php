<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel sudah tersedia di database sebelumnya.
        // Jangan membuat tabel duplikat.
        if (Schema::hasTable('laporan_masalah')) {
            return;
        }

        Schema::create('laporan_masalah', function (Blueprint $table) {
            $table->id('id_laporan_masalah');

            $table->unsignedBigInteger('id_pelapor');
            $table->string('jenis_objek');
            $table->unsignedBigInteger('id_objek');

            $table->enum('kategori_masalah', [
                'Kedaluwarsa',
                'Tidak sesuai',
                'Spam',
            ]);

            $table->text('deskripsi_masalah')->nullable();
            $table->string('bukti_foto')->nullable();

            $table->enum('status_laporan', [
                'Baru',
                'Ditinjau',
                'Ditindaklanjuti',
                'Ditolak',
            ])->default('Baru');

            $table->timestamp('waktu_laporan')->useCurrent();

            $table->foreign('id_pelapor')
                ->references('id_pengguna')
                ->on('pengguna');
        });
    }

    public function down(): void
    {
        // Tabel sudah ada sebelum migration ini dibuat.
        // Jangan hapus tabel dan data yang sudah tersimpan.
    }
};
