<?php

namespace App\Http\Controllers\ForumSharing;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForumSharing\SimpanPostinganRequest;
use App\Models\KlaimPorsi;
use App\Models\PostinganSharing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class PostinganSharingController extends Controller
{
    /** SCR-13.1: daftar postingan, urut terbaru (default). */
    public function index(Request $request)
    {
        $urut = $request->query('urut') === 'terlama' ? 'terlama' : 'terbaru';
        $arah = $urut === 'terlama' ? 'asc' : 'desc';

        $postingan = PostinganSharing::orderBy('waktu_dibuat', $arah)
            ->orderBy('id_postingan_sharing', $arah)
            ->get();

        return view('forum_sharing.index', compact('postingan', 'urut'));
    }

    /** SCR-13.2: form buat. */
    public function create()
    {
        return view('forum_sharing.create');
    }

    /** UC-13 langkah 4-6: simpan, status Aktif. */
    public function store(SimpanPostinganRequest $request)
    {
        $data = $request->validated();

        PostinganSharing::create([
            'id_pengunggah' => auth()->id(),
            'nama_makanan' => $data['nama_makanan'],
            'foto_makanan' => $request->file('foto_makanan')->store('postingan_sharing', 'public'),
            'sisa_porsi' => $data['jumlah_porsi'],
            'lokasi_pengambilan' => $data['lokasi_pengambilan'],
            'batas_waktu' => $data['batas_waktu'],
            'status_postingan' => PostinganSharing::STATUS_AKTIF,
        ]);

        return redirect()->route('forum.index')->with('sukses', 'Postingan berhasil diterbitkan.');
    }

    
    /** SCR-13.3: detail postingan + data PBI-14. */
    public function show(PostinganSharing $postingan)
    {
        $postingan->load('pengunggah');

        // Ambil komentar beserta nama penulis, urut dari terlama.
        $komentar = $postingan->komentar()
            ->with('penulis')
            ->orderBy('waktu_komentar')
            ->get();

        $idPengguna = auth()->id();

        // Cek apakah pengguna adalah pemilik postingan.
        $adalahPemilik = $postingan->dimilikiOleh($idPengguna);

        $bisaKelola = $adalahPemilik && $postingan->berstatusAktif();

        // Ambil klaim milik pengguna yang sedang login.
        $klaimSaya = $postingan->klaimPorsi()
            ->where('id_pengklaim', $idPengguna)
            ->orderByDesc('waktu_pengajuan')
            ->get();
        
        // Ambil semua klaim beserta data pengguna yang mengklaim.
        $semuaKlaim = $postingan->klaimPorsi()
            ->with('pengklaim')
            ->orderByDesc('waktu_pengajuan')
            ->get();

        // Pemilik atau pengguna dengan klaim yang tidak dibatalkan boleh berkomentar.
        $bolehKomentar = $adalahPemilik
            || $klaimSaya->contains(fn ($klaim) => $klaim->status_klaim !== KlaimPorsi::STATUS_DIBATALKAN);

        // Pengguna tidak boleh mengklaim postingannya sendiri.
        $bisaKlaim = !$adalahPemilik
            && $postingan->berstatusAktif()
            && $postingan->batas_waktu->isFuture()
            && $postingan->sisa_porsi > 0;

        return view('forum_sharing.show', compact(
            'postingan',
            'adalahPemilik',
            'bisaKelola',
            'komentar',
            'klaimSaya',
            'semuaKlaim',
            'bolehKomentar',
            'bisaKlaim'
        ));
    }


    /** SCR-13.4: form ubah. */
    public function edit(PostinganSharing $postingan)
    {
        $this->pastikanBisaKelola($postingan);

        return view('forum_sharing.edit', compact('postingan'));
    }

    /** UC-13 langkah 11-12: simpan perubahan. */
    public function update(SimpanPostinganRequest $request, PostinganSharing $postingan)
    {
        $this->pastikanBisaKelola($postingan);

        $data = $request->validated();

        $postingan->nama_makanan = $data['nama_makanan'];
        $postingan->sisa_porsi = $data['jumlah_porsi'];
        $postingan->lokasi_pengambilan = $data['lokasi_pengambilan'];
        $postingan->batas_waktu = $data['batas_waktu'];

        if ($request->hasFile('foto_makanan')) {
            Storage::disk('public')->delete($postingan->foto_makanan);
            $postingan->foto_makanan = $request->file('foto_makanan')->store('postingan_sharing', 'public');
        }

        $postingan->save();

        return redirect()->route('forum.show', $postingan)->with('sukses', 'Perubahan berhasil disimpan.');
    }

    /** SCR-13.5: tutup postingan, status menjadi Ditutup. */
    public function tutup(PostinganSharing $postingan)
    {
        $this->pastikanBisaKelola($postingan);

        $postingan->update(['status_postingan' => PostinganSharing::STATUS_DITUTUP]);

        return redirect()->route('forum.show', $postingan)->with('sukses', 'Postingan berhasil ditutup.');
    }

    /** SCR-13.6: hapus postingan beserta klaim dan komentar terkait. */
    public function destroy(PostinganSharing $postingan)
    {
        $this->pastikanBisaKelola($postingan);

        // TODO PBI-14: tabel klaim_porsi dan komentar dibuat di PBI-14.
        // Guard Schema::hasTable membuat kode ini aman sebelum tabelnya ada.
        $punyaKlaimDisetujui = Schema::hasTable('klaim_porsi')
            && DB::table('klaim_porsi')
                ->where('id_postingan_sharing', $postingan->id_postingan_sharing)
                ->where('status_klaim', 'Disetujui')
                ->exists();

        if ($punyaKlaimDisetujui) {
            return redirect()->route('forum.show', $postingan)->with(
                'galat',
                'Postingan ini sudah memiliki klaim yang disetujui sehingga tidak dapat dihapus. Gunakan “Tutup Postingan”.'
            );
        }

        DB::transaction(function () use ($postingan) {
            if (Schema::hasTable('komentar')) {
                DB::table('komentar')->where('id_postingan_sharing', $postingan->id_postingan_sharing)->delete();
            }
            if (Schema::hasTable('klaim_porsi')) {
                DB::table('klaim_porsi')->where('id_postingan_sharing', $postingan->id_postingan_sharing)->delete();
            }
            $postingan->delete();
        });

        Storage::disk('public')->delete($postingan->foto_makanan);

        return redirect()->route('forum.index')->with('sukses', 'Postingan berhasil dihapus.');
    }

    /** Alternatif A5: hanya pemilik, hanya status Aktif. */
    private function pastikanBisaKelola(PostinganSharing $postingan): void
    {
        abort_unless(
            $postingan->dimilikiOleh(auth()->id()) && $postingan->berstatusAktif(),
            403,
            'Anda tidak dapat mengelola postingan ini.'
        );
    }
}
