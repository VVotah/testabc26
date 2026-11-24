<?php

namespace App\Http\Controllers\AkunMitra;

use App\Http\Controllers\Controller;
use App\Http\Requests\AkunMitra\SimpanTitikRequest;
use App\Models\Mitra;
use App\Models\TitikPengambilan;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * PBI-03 (FOOD-11): Mitra/Donatur mengelola titik pengambilan miliknya.
 * Use case 4.1.3, mockup 4.5.10 dan 4.5.11. Route dijaga auth + peran:Mitra/Donatur;
 * mitra yang belum terverifikasi tetap boleh mengelola titik (sesuai use case).
 * Mitra hanya bisa melihat dan mengubah titik miliknya; titik milik orang lain dijawab 404.
 */
class TitikPengambilanController extends Controller
{
    /** lihatDaftarTitik() */
    public function index(Request $request): View
    {
        $mitra = $this->mitra($request);

        return view('akun_mitra.titik.index', [
            'daftarTitik' => $mitra->titikPengambilan()->orderBy('nama_titik')->get(),
        ]);
    }

    public function create(Request $request): View
    {
        $this->mitra($request);

        return view('akun_mitra.titik.form', ['titik' => new TitikPengambilan]);
    }

    /** tambahTitik() */
    public function store(SimpanTitikRequest $request): RedirectResponse
    {
        $titik = $this->mitra($request)->titikPengambilan()->create($request->validated());

        return redirect()->route('titik-pengambilan.index')
            ->with('sukses', "Titik \"{$titik->nama_titik}\" berhasil ditambahkan.");
    }

    /** lihatDetailTitik() */
    public function show(Request $request, TitikPengambilan $titik): View
    {
        $this->pastikanMilik($request, $titik);

        return view('akun_mitra.titik.show', [
            'titik' => $titik,
            'dipakaiListing' => $titik->cekTitikDipakaiListing(),
        ]);
    }

    public function edit(Request $request, TitikPengambilan $titik): View
    {
        $this->pastikanMilik($request, $titik);

        return view('akun_mitra.titik.form', ['titik' => $titik]);
    }

    /** ubahTitik() */
    public function update(SimpanTitikRequest $request, TitikPengambilan $titik): RedirectResponse
    {
        $this->pastikanMilik($request, $titik);

        $titik->update($request->validated());

        return redirect()->route('titik-pengambilan.index')
            ->with('sukses', "Perubahan titik \"{$titik->nama_titik}\" tersimpan.");
    }

    /** hapusTitik(): ditolak bila titik masih dipakai listing Aktif. */
    public function destroy(Request $request, TitikPengambilan $titik): RedirectResponse
    {
        $this->pastikanMilik($request, $titik);

        $dipakai = $titik->cekTitikDipakaiListing();

        if ($dipakai > 0) {
            return back()->with('galat', "Titik \"{$titik->nama_titik}\" tidak bisa dihapus karena masih dipakai {$dipakai} listing aktif. Tutup atau pindahkan listing itu dulu.");
        }

        try {
            $titik->delete();
        } catch (QueryException) {
            // Masih dirujuk data lain (mis. riwayat listing atau pickup) lewat foreign key.
            return back()->with('galat', "Titik \"{$titik->nama_titik}\" tidak bisa dihapus karena masih tercatat di riwayat listing atau pickup.");
        }

        return redirect()->route('titik-pengambilan.index')
            ->with('sukses', "Titik \"{$titik->nama_titik}\" dihapus.");
    }

    /** Data usaha mitra yang sedang login. Akun mitra tanpa data usaha tidak bisa mengelola titik. */
    private function mitra(Request $request): Mitra
    {
        // Selalu baca dari database supaya status mitra yang dipakai adalah yang terbaru.
        $mitra = $request->user()->mitra()->first();

        abort_unless($mitra, 403, 'Akun ini belum punya data usaha mitra.');

        return $mitra;
    }

    /** Titik milik mitra lain dijawab 404 supaya keberadaannya tidak bocor. */
    private function pastikanMilik(Request $request, TitikPengambilan $titik): void
    {
        abort_unless($titik->id_mitra === $this->mitra($request)->id_mitra, 404);
    }
}
