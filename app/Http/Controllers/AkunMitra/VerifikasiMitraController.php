<?php

namespace App\Http\Controllers\AkunMitra;

use App\Http\Controllers\Controller;
use App\Models\Mitra;
use App\Models\VerifikasiMitra;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Fondasi (FOOD-33): verifikasi mitra oleh Admin.
 * Diagram kelas 4.4.1: lihatDaftarPengajuan(), lihatDetailPengajuan(), setujui(), tolak().
 * Semua route dijaga auth + peran:Admin (routes/modul/akun_mitra.php).
 */
class VerifikasiMitraController extends Controller
{
    /** Tab saring: kunci di URL => keputusan di tabel (null = belum diputuskan). */
    private const SARINGAN = [
        'menunggu' => null,
        'terverifikasi' => VerifikasiMitra::KEPUTUSAN_SETUJU,
        'ditolak' => VerifikasiMitra::KEPUTUSAN_TOLAK,
    ];

    /** lihatDaftarPengajuan() */
    public function index(Request $request): View
    {
        $saring = array_key_exists($request->query('status'), self::SARINGAN)
            ? $request->query('status')
            : 'menunggu';

        $query = VerifikasiMitra::with('mitra.pengguna');

        $saring === 'menunggu'
            ? $query->menunggu()->oldest('tanggal_pengajuan')   // yang paling lama menunggu di atas
            : $query->where('keputusan', self::SARINGAN[$saring])->latest('tanggal_keputusan');

        $jumlah = [
            'menunggu' => VerifikasiMitra::menunggu()->count(),
            'terverifikasi' => VerifikasiMitra::where('keputusan', VerifikasiMitra::KEPUTUSAN_SETUJU)->count(),
            'ditolak' => VerifikasiMitra::where('keputusan', VerifikasiMitra::KEPUTUSAN_TOLAK)->count(),
        ];

        return view('akun_mitra.verifikasi.index', [
            'pengajuan' => $query->paginate(15)->withQueryString(),
            'saring' => $saring,
            'jumlah' => $jumlah,
            'label' => [
                'menunggu' => Mitra::STATUS_MENUNGGU,
                'terverifikasi' => Mitra::STATUS_TERVERIFIKASI,
                'ditolak' => Mitra::STATUS_DITOLAK,
            ],
        ]);
    }

    /** lihatDetailPengajuan() */
    public function show(VerifikasiMitra $pengajuan): View
    {
        $pengajuan->load('mitra.pengguna', 'admin');

        $riwayat = VerifikasiMitra::with('admin')
            ->where('id_mitra', $pengajuan->id_mitra)
            ->oldest('tanggal_pengajuan')
            ->get();

        $ekstensi = strtolower(pathinfo($pengajuan->bukti_pendukung, PATHINFO_EXTENSION));

        return view('akun_mitra.verifikasi.show', [
            'pengajuan' => $pengajuan,
            'mitra' => $pengajuan->mitra,
            'riwayat' => $riwayat,
            'dokumenGambar' => in_array($ekstensi, ['jpg', 'jpeg', 'png'], true),
            'namaDokumen' => 'Dokumen verifikasi.'.$ekstensi,
        ]);
    }

    /**
     * Kirim dokumen bukti dari disk privat, hanya untuk Admin (NFR-03).
     * Lokasi berkas diambil dari database, tidak pernah dari URL.
     */
    public function dokumen(VerifikasiMitra $pengajuan): Response
    {
        $disk = Storage::disk('local');

        abort_unless($pengajuan->bukti_pendukung && $disk->exists($pengajuan->bukti_pendukung), 404, 'Dokumen tidak ditemukan.');

        $ekstensi = strtolower(pathinfo($pengajuan->bukti_pendukung, PATHINFO_EXTENSION));

        return $disk->response($pengajuan->bukti_pendukung, 'dokumen-verifikasi-'.$pengajuan->id_verifikasi.'.'.$ekstensi, [
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ], 'inline');
    }

    /** setujui() atau tolak() */
    public function putuskan(Request $request, VerifikasiMitra $pengajuan): RedirectResponse
    {
        $data = $request->validate([
            'keputusan' => ['required', Rule::in(['setujui', 'tolak'])],
            'alasan' => ['nullable', 'required_if:keputusan,tolak', 'string', 'max:255'],
        ], [
            'keputusan.required' => 'Pilih setujui atau tolak.',
            'keputusan.in' => 'Pilih setujui atau tolak.',
            'alasan.required_if' => 'Alasan penolakan wajib diisi.',
            'alasan.max' => 'Alasan maksimal 255 karakter.',
        ]);

        $admin = $request->user();
        $namaMitra = $pengajuan->mitra->nama_mitra;

        if ($data['keputusan'] === 'setujui') {
            $pengajuan->setujui($admin);
            $pesan = "{$namaMitra} disetujui. Mitra ini sekarang bisa membuat listing.";
        } else {
            $pengajuan->tolak($admin, $data['alasan']);
            $pesan = "Pengajuan {$namaMitra} ditolak. Alasannya tampil di dashboard mitra.";
        }

        return redirect()->route('verifikasi-mitra.index')->with('sukses', $pesan);
    }
}
