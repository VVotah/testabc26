<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LaporanMasalah extends Model
{
    public const OBJEK_LISTING = 'Listing';
    public const OBJEK_AKUN = 'Akun';
    public const OBJEK_POSTINGAN_SHARING = 'Postingan sharing';

    public const STATUS_BARU = 'Baru';
    public const STATUS_DITINJAU = 'Ditinjau';
    public const STATUS_DITINDAKLANJUTI = 'Ditindaklanjuti';
    public const STATUS_DITOLAK = 'Ditolak';

    /** Status yang bisa dipilih Admin saat menindaklanjuti (UC 4.1.19). */
    public const STATUS_TINDAK_LANJUT = [
        self::STATUS_DITINJAU,
        self::STATUS_DITINDAKLANJUTI,
        self::STATUS_DITOLAK,
    ];

    /** Model untuk tiap jenis objek yang bisa dilaporkan. */
    public const KELAS_OBJEK = [
        self::OBJEK_LISTING => Listing::class,
        self::OBJEK_AKUN => Pengguna::class,
        self::OBJEK_POSTINGAN_SHARING => PostinganSharing::class,
    ];

    protected $table = 'laporan_masalah';

    protected $primaryKey = 'id_laporan_masalah';

    public $timestamps = false;

    protected $fillable = [
        'id_pelapor',
        'jenis_objek',
        'id_objek',
        'kategori_masalah',
        'deskripsi_masalah',
        'bukti_foto',
        'status_laporan',
        'waktu_laporan',
    ];

    protected $casts = [
        'id_pelapor' => 'integer',
        'id_objek' => 'integer',
        'waktu_laporan' => 'datetime',
    ];

    /** Objek yang dilaporkan, disimpan setelah dimuat supaya tidak di-query berulang. */
    private ?Model $objekTermuat = null;

    private bool $objekSudahDimuat = false;

    public function pelapor(): BelongsTo
    {
        return $this->belongsTo(
            Pengguna::class,
            'id_pelapor',
            'id_pengguna'
        );
    }

    public function tindakLanjut(): HasMany
    {
        return $this->hasMany(
            TindakLanjut::class,
            'id_laporan_masalah',
            'id_laporan_masalah'
        );
    }

    /** Muat objek yang dilaporkan untuk banyak laporan sekaligus (satu query per jenis objek). */
    public static function muatObjek(iterable $daftarLaporan): void
    {
        foreach (collect($daftarLaporan)->groupBy('jenis_objek') as $jenis => $kelompok) {
            $kelas = self::KELAS_OBJEK[$jenis] ?? null;
            $objek = $kelas
                ? $kelas::whereKey($kelompok->pluck('id_objek')->unique()->all())->get()->keyBy(fn ($o) => $o->getKey())
                : collect();

            foreach ($kelompok as $laporan) {
                $laporan->objekTermuat = $objek->get($laporan->id_objek);
                $laporan->objekSudahDimuat = true;
            }
        }
    }

    /** Listing, akun, atau postingan sharing yang dilaporkan; null bila sudah tidak ada. */
    public function objekDilaporkan(): ?Model
    {
        if (! $this->objekSudahDimuat) {
            $kelas = self::KELAS_OBJEK[$this->jenis_objek] ?? null;
            $this->objekTermuat = $kelas ? $kelas::find($this->id_objek) : null;
            $this->objekSudahDimuat = true;
        }

        return $this->objekTermuat;
    }

    /** Nama makanan atau nama akun yang dilaporkan, untuk tampilan. */
    public function getNamaObjekAttribute(): string
    {
        $objek = $this->objekDilaporkan();

        return match (true) {
            $objek instanceof Listing, $objek instanceof PostinganSharing => $objek->nama_makanan,
            $objek instanceof Pengguna => $objek->nama,
            default => $this->jenis_objek.' sudah tidak tersedia',
        };
    }

    /** Status objek yang dilaporkan (Aktif, Ditangguhkan, dst.), null bila objek tidak ada. */
    public function getStatusObjekAttribute(): ?string
    {
        $objek = $this->objekDilaporkan();

        return match (true) {
            $objek instanceof Listing => $objek->status_listing,
            $objek instanceof Pengguna => $objek->status_akun,
            $objek instanceof PostinganSharing => $objek->status_postingan,
            default => null,
        };
    }

    /**
     * Alasan objek tidak bisa ditangguhkan, atau null bila boleh.
     * Yang bisa ditangguhkan hanya listing dan akun non-Admin (UC 4.1.19).
     */
    public function alasanTidakBisaDitangguhkan(): ?string
    {
        if (! in_array($this->jenis_objek, [self::OBJEK_LISTING, self::OBJEK_AKUN], true)) {
            return 'Hanya listing atau akun yang dapat ditangguhkan.';
        }

        $objek = $this->objekDilaporkan();

        if (! $objek) {
            return $this->jenis_objek.' yang dilaporkan sudah tidak ada.';
        }

        if ($objek instanceof Pengguna && $objek->punyaPeran(Peran::ADMIN)) {
            return 'Akun Admin tidak dapat ditangguhkan.';
        }

        if ($this->status_objek === 'Ditangguhkan') {
            return $this->jenis_objek.' ini sudah berstatus Ditangguhkan.';
        }

        return null;
    }
}
