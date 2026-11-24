<?php

namespace App\Http\Controllers\Listing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Listing\SimpanJadwalSurplusRequest;
use App\Models\JadwalSurplus;
use App\Models\Mitra;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JadwalSurplusController extends Controller
{
    /**
     * Menampilkan daftar jadwal surplus milik mitra yang login.
     */
    public function index(Request $request): View
    {
        $mitra = $this->mitra($request);

        return view('listing.jadwal-surplus.index', [
            'daftarJadwal' => JadwalSurplus::where(
                'id_mitra',
                $mitra->id_mitra
            )
                ->orderBy('id_jadwal_surplus')
                ->get(),
        ]);
    }

    /**
     * Menampilkan form tambah jadwal surplus.
     */
   public function create(Request $request): View
{
    $this->mitra($request);

    return view('listing.jadwal-surplus.form', [
        'jadwal' => new JadwalSurplus(),
    ]);
}

    /**
     * Menyimpan jadwal surplus baru.
     */
    public function store(
        SimpanJadwalSurplusRequest $request
    ): RedirectResponse {
        $mitra = $this->mitra($request);

        $data = $request->validated();
        $data['id_mitra'] = $mitra->id_mitra;

        JadwalSurplus::create($data);

        return redirect()
            ->route('listing.jadwal-surplus.index')
            ->with('sukses', 'Jadwal surplus berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit jadwal surplus.
     */
    public function edit(
        Request $request,
        JadwalSurplus $jadwalSurplus
    ): View {
        $mitra = $this->mitra($request);

        abort_unless(
            $jadwalSurplus->id_mitra == $mitra->id_mitra,
            404
        );

        return view('listing.jadwal-surplus.form', [
            'jadwal' => $jadwalSurplus,
        ]);
    }

    /**
     * Memperbarui jadwal surplus milik mitra.
     */
    public function update(
        SimpanJadwalSurplusRequest $request,
        JadwalSurplus $jadwalSurplus
    ): RedirectResponse {
        $mitra = $this->mitra($request);

        abort_unless(
            $jadwalSurplus->id_mitra == $mitra->id_mitra,
            404
        );

        $jadwalSurplus->update($request->validated());

        return redirect()
            ->route('listing.jadwal-surplus.index')
            ->with('sukses', 'Jadwal surplus berhasil diperbarui.');
    }

    /**
     * Menghapus jadwal surplus milik mitra.
     */
    public function destroy(
        Request $request,
        JadwalSurplus $jadwalSurplus
    ): RedirectResponse {
        $mitra = $this->mitra($request);

        abort_unless(
            $jadwalSurplus->id_mitra == $mitra->id_mitra,
            404
        );

        $jadwalSurplus->delete();

        return redirect()
            ->route('listing.jadwal-surplus.index')
            ->with('sukses', 'Jadwal surplus berhasil dihapus.');
    }

    /**
     * Mengambil data mitra dari pengguna yang sedang login.
     */
    private function mitra(Request $request): Mitra
    {
        $mitra = $request->user()->mitra()->first();

        abort_unless(
            $mitra,
            403,
            'Akun ini belum punya data usaha mitra.'
        );

        return $mitra;
    }
}
