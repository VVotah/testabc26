<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Validation\Rule;

/**
 * Tabel mitra (ERD 4.3.1). Satu akun pengguna berperan Mitra/Donatur punya satu data mitra.
 *
 * Kolom alamat menyimpan alamat lengkap beserta kota dan kode pos (sesuai ERD),
 * dengan format tetap "<alamat jalan>, <kota>, <kode pos>". Untuk membaca bagiannya:
 *   $mitra->alamat_jalan, $mitra->kota, $mitra->kode_pos
 * Untuk menyimpan: 'alamat' => Mitra::gabungAlamat($jalan, $kota, $kodePos)
 */
class Mitra extends Model
{
    public const STATUS_MENUNGGU = 'Menunggu verifikasi';
    public const STATUS_TERVERIFIKASI = 'Terverifikasi';
    public const STATUS_DITOLAK = 'Ditolak';
    public const STATUS_DITANGGUHKAN = 'Ditangguhkan';

    /** Jenis mitra sesuai ERD 4.3.1 (kolom jenis_mitra). */
    public const JENIS = ['Kantin', 'Restoran', 'Toko roti', 'Katering', 'Panitia acara'];

    protected $table = 'mitra';

    protected $primaryKey = 'id_mitra';

    protected $fillable = [
        'id_pengguna',
        'nama_mitra',
        'jenis_mitra',
        'alamat',
        'status_mitra',
    ];

    /**
     * Aturan validasi data usaha, dipakai bersama oleh form daftar mitra dan form
     * Profil Mitra (PBI-02) supaya keduanya selalu sama.
     */
    public static function aturanDataUsaha(): array
    {
        return [
            'nama_mitra' => ['required', 'string', 'max:100'],
            'jenis_mitra' => ['required', Rule::in(self::JENIS)],
            'alamat_jalan' => ['required', 'string', 'max:180'],
            // Tanpa koma, karena kota disimpan di kolom alamat dengan pemisah koma.
            'kota' => ['required', 'string', 'max:50', 'not_regex:/,/'],
            'kode_pos' => ['required', 'digits:5'],
        ];
    }

    public static function pesanDataUsaha(): array
    {
        return [
            'nama_mitra.required' => 'Nama usaha wajib diisi.',
            'nama_mitra.max' => 'Nama usaha maksimal 100 karakter.',
            'jenis_mitra.required' => 'Pilih jenis mitra.',
            'jenis_mitra.in' => 'Pilih jenis mitra dari daftar.',
            'alamat_jalan.required' => 'Alamat lengkap wajib diisi.',
            'alamat_jalan.max' => 'Alamat lengkap maksimal 180 karakter.',
            'kota.required' => 'Kota wajib diisi.',
            'kota.not_regex' => 'Tulis nama kota saja, tanpa koma.',
            'kode_pos.required' => 'Kode pos wajib diisi.',
            'kode_pos.digits' => 'Kode pos harus 5 angka.',
        ];
    }

    /** Aturan dokumen bukti verifikasi (PDF/JPG/PNG, maks. 5 MB). */
    public static function aturanDokumen(bool $wajib = true): array
    {
        return ['bukti_pendukung' => [$wajib ? 'required' : 'nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120']];
    }

    public static function pesanDokumen(): array
    {
        return [
            'bukti_pendukung.required' => 'Unggah dokumen verifikasi (NIB atau KTP penanggung jawab).',
            'bukti_pendukung.file' => 'Dokumen gagal diunggah. Coba pilih ulang berkasnya.',
            'bukti_pendukung.uploaded' => 'Dokumen gagal diunggah. Pastikan ukurannya maksimal 5 MB.',
            'bukti_pendukung.mimes' => 'Dokumen harus berformat PDF, JPG, atau PNG.',
            'bukti_pendukung.max' => 'Ukuran dokumen maksimal 5 MB.',
        ];
    }

    /**
     * Diagram kelas: ubahProfilMitra(). Hanya data usaha yang berubah; status mitra
     * tetap dan tidak ada verifikasi ulang (FR-02, use case 4.1.2).
     *
     * @param  array{nama_mitra: string, jenis_mitra: string, alamat_jalan: string, kota: string, kode_pos: string}  $data
     */
    public function ubahProfilMitra(array $data): void
    {
        $this->update([
            'nama_mitra' => $data['nama_mitra'],
            'jenis_mitra' => $data['jenis_mitra'],
            'alamat' => self::gabungAlamat($data['alamat_jalan'], $data['kota'], $data['kode_pos']),
        ]);
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }

    public function verifikasi(): HasMany
    {
        return $this->hasMany(VerifikasiMitra::class, 'id_mitra', 'id_mitra');
    }

    /** Titik pengambilan milik mitra (PBI-03). */
    public function titikPengambilan(): HasMany
    {
        return $this->hasMany(TitikPengambilan::class, 'id_mitra', 'id_mitra');
    }

    /** Pengajuan verifikasi paling baru. */
    public function verifikasiTerakhir(): HasOne
    {
        return $this->hasOne(VerifikasiMitra::class, 'id_mitra', 'id_mitra')->latestOfMany('id_verifikasi');
    }

    /** Diagram kelas: cekTerverifikasi(). Hanya mitra Terverifikasi yang boleh membuat listing. */
    public function terverifikasi(): bool
    {
        return $this->status_mitra === self::STATUS_TERVERIFIKASI;
    }

    public static function gabungAlamat(string $jalan, string $kota, string $kodePos): string
    {
        return trim($jalan).', '.trim($kota).', '.trim($kodePos);
    }

    /**
     * Pecah kolom alamat menjadi [jalan, kota, kode pos], dibaca dari kanan
     * karena alamat jalan boleh berisi koma.
     *
     * @return array{0: string, 1: string, 2: string}
     */
    public function pecahAlamat(): array
    {
        $bagian = explode(', ', (string) $this->alamat);

        if (count($bagian) < 3) {
            return [(string) $this->alamat, '', ''];
        }

        $kodePos = array_pop($bagian);
        $kota = array_pop($bagian);

        return [implode(', ', $bagian), $kota, $kodePos];
    }

    public function getAlamatJalanAttribute(): string
    {
        return $this->pecahAlamat()[0];
    }

    public function getKotaAttribute(): string
    {
        return $this->pecahAlamat()[1];
    }

    public function getKodePosAttribute(): string
    {
        return $this->pecahAlamat()[2];
    }
}
