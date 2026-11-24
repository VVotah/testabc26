<?php

namespace App\Http\Controllers\ForumSharing;

use App\Http\Controllers\Controller;
use App\Models\KlaimPorsi;
use App\Models\PostinganSharing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KlaimPorsiController extends Controller
{
    /**
     * Mengajukan klaim porsi.
     */
    public function store(Request $request, PostinganSharing $postingan)
    {
        $data = $request->validate([
            'jumlah_porsi' => ['required', 'integer', 'min:1'],
        ], [
            'jumlah_porsi.required' => 'Jumlah porsi wajib diisi.',
            'jumlah_porsi.integer' => 'Jumlah porsi harus berupa angka.',
            'jumlah_porsi.min' => 'Jumlah porsi minimal 1.',
        ]);

        return DB::transaction(function () use ($postingan, $data) {
            $postingan = PostinganSharing::whereKey($postingan->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if ($postingan->dimilikiOleh(auth()->id())) {
                return back()->with('galat', 'Anda tidak dapat mengklaim postingan sendiri.');
            }

            if (!$postingan->berstatusAktif()) {
                return back()->with('galat', 'Postingan ini sudah tidak aktif.');
            }

            if ($postingan->batas_waktu->lessThanOrEqualTo(now())) {
                return back()->with('galat', 'Batas waktu pengambilan makanan sudah berakhir.');
            }

            if ($postingan->sisa_porsi < 1) {
                return back()->with('galat', 'Porsi makanan sudah habis.');
            }

            if ($data['jumlah_porsi'] > $postingan->sisa_porsi) {
                return back()->with(
                    'galat',
                    'Jumlah klaim melebihi sisa porsi yang tersedia.'
                );
            }

            KlaimPorsi::create([
                'id_postingan_sharing' => $postingan->id_postingan_sharing,
                'id_pengklaim' => auth()->id(),
                'jumlah_porsi' => $data['jumlah_porsi'],
                'status_klaim' => KlaimPorsi::STATUS_MENUNGGU,
            ]);

            return redirect()
                ->route('forum.show', $postingan)
                ->with('sukses', 'Klaim porsi berhasil diajukan.');
        });
    }

    /**
     * Membatalkan klaim milik pengguna.
     */
    public function destroy(
        PostinganSharing $postingan,
        KlaimPorsi $klaimPorsi
    ) {
        abort_unless(
            (int) $klaimPorsi->id_postingan_sharing ===
            (int) $postingan->id_postingan_sharing,
            404
        );

        abort_unless(
            (int) $klaimPorsi->id_pengklaim === (int) auth()->id(),
            403,
            'Anda tidak dapat membatalkan klaim milik pengguna lain.'
        );

        if ($klaimPorsi->status_klaim !== KlaimPorsi::STATUS_MENUNGGU) {
            return back()->with(
                'galat',
                'Klaim hanya dapat dibatalkan selama menunggu persetujuan.'
            );
        }

        $klaimPorsi->update([
            'status_klaim' => KlaimPorsi::STATUS_DIBATALKAN,
        ]);

        return redirect()
            ->route('forum.show', $postingan)
            ->with('sukses', 'Klaim berhasil dibatalkan.');
    }
}
