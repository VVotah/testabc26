<?php

namespace App\Http\Controllers\ModerasiKualitas;

use App\Http\Controllers\Controller;
use App\Http\Requests\ModerasiKualitas\SimpanLaporanMasalahRequest;
use App\Models\LaporanMasalah;
use Illuminate\Support\Facades\Storage;

class LaporanMasalahController extends Controller
{
    /**
     * Menyimpan laporan masalah dari pengguna.
     */
    public function store(SimpanLaporanMasalahRequest $request)
    {
        $validated = $request->validated();

        $pathFoto = null;

        if ($request->hasFile('bukti_foto')) {
            $pathFoto = $request->file('bukti_foto')
                ->store('bukti_laporan', 'public');
        }

        try {
            LaporanMasalah::create([
                'id_pelapor' => auth()->id(),
                'jenis_objek' => $validated['jenis_objek'],
                'id_objek' => $validated['id_objek'],
                'kategori_masalah' => $validated['kategori_masalah'],
                'deskripsi_masalah' => $validated['deskripsi_masalah'] ?? null,
                'bukti_foto' => $pathFoto,
            ]);
        } catch (\Throwable $exception) {
            if ($pathFoto !== null) {
                Storage::disk('public')->delete($pathFoto);
            }

            throw $exception;
        }

        return redirect()->back()
            ->with('sukses', 'Laporan berhasil dikirim.');
    }
}
