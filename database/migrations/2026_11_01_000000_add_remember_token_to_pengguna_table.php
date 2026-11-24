<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kolom teknis untuk fitur "Ingat saya" di halaman login.
     * Bukan atribut ERD 4.3.1; dibutuhkan oleh mekanisme login Laravel.
     */
    public function up(): void
    {
        Schema::table('pengguna', function (Blueprint $table) {
            $table->rememberToken()->after('status_akun');
        });
    }

    public function down(): void
    {
        Schema::table('pengguna', function (Blueprint $table) {
            $table->dropRememberToken();
        });
    }
};
