<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Tabel verifikasi_mitra (ERD 4.3.1): riwayat pengajuan verifikasi mitra.
 * keputusan, alasan, id_admin, dan tanggal_keputusan kosong selama Menunggu verifikasi.
 * bukti_pendukung = lokasi berkas di disk privat 'local' (storage/app/private),
 * hanya boleh dibuka Admin (NFR-03).
 *
 * Pengajuan (diagram kelas 4.4.1): VerifikasiMitra::buatPengajuan($mitra, $berkas) untuk
 * pengajuan ulang setelah ditolak (ERD: satu mitra punya beberapa pengajuan sebagai riwayat).
 *
 * Keputusan Admin (diagram kelas 4.4.1): $pengajuan->setujui($admin) dan
 * $pengajuan->tolak($admin, $alasan). Keduanya juga mengubah mitra.status_mitra.
 */
class VerifikasiMitra extends Model
{
    public const KEPUTUSAN_SETUJU = 'Terverifikasi';
    public const KEPUTUSAN_TOLAK = 'Ditolak';

    protected $table = 'verifikasi_mitra';

    protected $primaryKey = 'id_verifikasi';

    protected $fillable = [
        'id_mitra',
        'bukti_pendukung',
        'tanggal_pengajuan',
        'keputusan',
        'alasan',
        'id_admin',
        'tanggal_keputusan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pengajuan' => 'datetime',
            'tanggal_keputusan' => 'datetime',
        ];
    }

    public function mitra(): BelongsTo
    {
        return $this->belongsTo(Mitra::class, 'id_mitra', 'id_mitra');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_admin', 'id_pengguna');
    }

    /** Pengajuan yang belum diputuskan. */
    public function scopeMenunggu(Builder $query): Builder
    {
        return $query->whereNull('keputusan');
    }

    public function sudahDiputuskan(): bool
    {
        return $this->keputusan !== null;
    }

    /** Status yang ditampilkan untuk pengajuan ini (status baku mitra). */
    public function getStatusAttribute(): string
    {
        return $this->keputusan ?? Mitra::STATUS_MENUNGGU;
    }

    /**
     * Diagram kelas: buatPengajuan(). Membuat pengajuan baru dan mengembalikan status mitra
     * ke Menunggu verifikasi. Hanya untuk mitra Ditolak (pengajuan ulang); pengajuan pertama
     * dibuat saat daftar mitra.
     */
    public static function buatPengajuan(Mitra $mitra, string $buktiPendukung): self
    {
        return DB::transaction(function () use ($mitra, $buktiPendukung) {
            $mitra = Mitra::whereKey($mitra->getKey())->lockForUpdate()->firstOrFail();

            if ($mitra->status_mitra !== Mitra::STATUS_DITOLAK) {
                throw ValidationException::withMessages([
                    'bukti_pendukung' => 'Pengajuan ulang hanya untuk mitra yang pengajuannya ditolak.',
                ]);
            }

            $pengajuan = self::create([
                'id_mitra' => $mitra->id_mitra,
                'bukti_pendukung' => $buktiPendukung,
                'tanggal_pengajuan' => now(),
            ]);

            $mitra->update(['status_mitra' => Mitra::STATUS_MENUNGGU]);

            return $pengajuan;
        });
    }

    public function setujui(Pengguna $admin): void
    {
        $this->putuskan($admin, self::KEPUTUSAN_SETUJU, null);
    }

    public function tolak(Pengguna $admin, string $alasan): void
    {
        if (trim($alasan) === '') {
            throw ValidationException::withMessages(['alasan' => 'Alasan penolakan wajib diisi.']);
        }

        $this->putuskan($admin, self::KEPUTUSAN_TOLAK, trim($alasan));
    }

    /**
     * Simpan keputusan dan ubah status mitra dalam satu transaksi. Baris dikunci
     * supaya dua Admin yang menekan bersamaan tidak memutuskan pengajuan yang sama dua kali.
     */
    private function putuskan(Pengguna $admin, string $keputusan, ?string $alasan): void
    {
        DB::transaction(function () use ($admin, $keputusan, $alasan) {
            $pengajuan = self::whereKey($this->getKey())->lockForUpdate()->firstOrFail();

            if ($pengajuan->sudahDiputuskan()) {
                throw ValidationException::withMessages([
                    'keputusan' => 'Pengajuan ini sudah diputuskan sebelumnya.',
                ]);
            }

            $pengajuan->update([
                'keputusan' => $keputusan,
                'alasan' => $alasan,
                'id_admin' => $admin->id_pengguna,
                'tanggal_keputusan' => now(),
            ]);

            $pengajuan->mitra->update(['status_mitra' => $keputusan]);

            $this->setRawAttributes($pengajuan->getAttributes(), true);
        });
    }
}
