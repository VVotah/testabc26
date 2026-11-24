<?php

namespace App\Http\Controllers\ForumSharing;

use App\Http\Controllers\Controller;
use App\Models\Komentar;
use App\Models\KlaimPorsi;
use App\Models\PostinganSharing;
use Illuminate\Http\Request;

class KomentarController extends Controller
{
    public function store(Request $request, PostinganSharing $postingan)
    {
        $data = $request->validate([
            'isi_komentar' => ['required', 'string', 'max:1000'],
        ], [
            'isi_komentar.required' => 'Komentar tidak boleh kosong.',
            'isi_komentar.max' => 'Komentar maksimal 1000 karakter.',
        ]);

        $idPengguna = auth()->id();

        // Pengklaim yang semua klaimnya sudah dibatalkan tidak lagi ikut berkoordinasi.
        $bolehKomentar = $postingan->dimilikiOleh($idPengguna)
            || KlaimPorsi::where('id_postingan_sharing', $postingan->id_postingan_sharing)
                ->where('id_pengklaim', $idPengguna)
                ->where('status_klaim', '!=', KlaimPorsi::STATUS_DIBATALKAN)
                ->exists();

        if (!$bolehKomentar) {
            abort(403, 'Hanya pemilik postingan atau pengguna dengan klaim yang tidak dibatalkan yang dapat berkomentar.');
        }

        Komentar::create([
            'id_postingan_sharing' => $postingan->id_postingan_sharing,
            'id_penulis' => $idPengguna,
            'isi_komentar' => $data['isi_komentar'],
        ]);

        return redirect()->route('forum.show', $postingan)
            ->with('sukses', 'Komentar berhasil ditambahkan.');
    }

    public function destroy(PostinganSharing $postingan, Komentar $komentar)
    {
        abort_unless(
            (int) $komentar->id_postingan_sharing === (int) $postingan->id_postingan_sharing,
            404
        );

        abort_unless(
            (int) $komentar->id_penulis === (int) auth()->id(),
            403,
            'Anda tidak dapat menghapus komentar milik pengguna lain.'
        );

        $komentar->delete();

        return redirect()->route('forum.show', $postingan)
            ->with('sukses', 'Komentar berhasil dihapus.');
    }
}
