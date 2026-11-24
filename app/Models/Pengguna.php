<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Tabel pengguna (ERD 4.3.1). Dipakai sebagai model login (config/auth.php).
 *
 * Catatan untuk login: Auth::attempt() tetap memakai kunci 'password',
 * contoh: Auth::attempt(['email' => $email, 'password' => $kataSandi]).
 * Laravel mencocokkannya dengan kolom kata_sandi lewat getAuthPassword().
 */
class Pengguna extends Authenticatable
{
    public const STATUS_AKTIF = 'Aktif';
    public const STATUS_DITANGGUHKAN = 'Ditangguhkan';

    protected $table = 'pengguna';

    protected $primaryKey = 'id_pengguna';

    protected $fillable = [
        'id_peran',
        'nama',
        'email',
        'kata_sandi',
        'status_akun',
    ];

    protected $hidden = [
        'kata_sandi',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            // Otomatis di-hash (bcrypt) saat diisi, sesuai NFR-02.
            'kata_sandi' => 'hashed',
        ];
    }

    /**
     * Nama kolom kata sandi untuk autentikasi.
     */
    public function getAuthPasswordName(): string
    {
        return 'kata_sandi';
    }

    /**
     * Kata sandi (hash) untuk autentikasi.
     */
    public function getAuthPassword(): string
    {
        return $this->kata_sandi;
    }

    public function peran(): BelongsTo
    {
        return $this->belongsTo(Peran::class, 'id_peran', 'id_peran');
    }

    /** Data usaha, hanya untuk akun berperan Mitra/Donatur. */
    public function mitra(): HasOne
    {
        return $this->hasOne(Mitra::class, 'id_pengguna', 'id_pengguna');
    }

    /**
     * Akun boleh dipakai login hanya jika berstatus Aktif.
     */
    public function aktif(): bool
    {
        return $this->status_akun === self::STATUS_AKTIF;
    }

    /**
     * Cek peran akun, contoh: $pengguna->punyaPeran(Peran::ADMIN).
     */
    public function punyaPeran(string ...$namaPeran): bool
    {
        return in_array($this->peran?->nama_peran, $namaPeran, true);
    }
}
