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
        if (Schema::hasTable('tindak_lanjut')) {
            return;
        }

        Schema::create('tindak_lanjut', function (Blueprint $table) {
            $table->id('id_tindak_lanjut');

            $table->unsignedBigInteger('id_laporan_masalah');
            $table->unsignedBigInteger('id_admin');

            $table->string('jenis_tindakan');
            $table->string('objek_ditangguhkan')->nullable();
            $table->unsignedBigInteger('id_objek_ditangguhkan')->nullable();

            $table->text('balasan_pelapor')->nullable();
            $table->timestamp('waktu_tindak_lanjut')->useCurrent();

            $table->foreign('id_laporan_masalah')
                ->references('id_laporan_masalah')
                ->on('laporan_masalah')
                ->onDelete('cascade');

            $table->foreign('id_admin')
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
