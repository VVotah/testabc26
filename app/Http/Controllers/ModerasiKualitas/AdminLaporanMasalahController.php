<?php

namespace App\Http\Controllers\ModerasiKualitas;

use App\Http\Controllers\Controller;
use App\Models\LaporanMasalah;
use App\Models\Listing;
use App\Models\Pengguna;
use App\Models\TindakLanjut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AdminLaporanMasalahController extends Controller
{
    /**
     * Menampilkan daftar laporan masalah.
     */
    public function index()
    {
        $laporan = LaporanMasalah::with('pelapor')
            ->orderByDesc('waktu_laporan')
            ->orderByDesc('id_laporan_masalah')
            ->paginate(10);

        LaporanMasalah::muatObjek($laporan->items());

        return view(
            'moderasi_kualitas.admin_laporan.index',
            compact('laporan')
        );
    }

    /**
     * Menampilkan detail laporan masalah.
     */
    public function show(int $id)
    {
        $laporan = LaporanMasalah::with([
            'pelapor',
            'tindakLanjut' => fn ($q) => $q->orderBy('waktu_tindak_lanjut')->orderBy('id_tindak_lanjut'),
            'tindakLanjut.admin',
        ])->findOrFail($id);

        return view(
            'moderasi_kualitas.admin_laporan.show',
            [
                'laporan' => $laporan,
                'alasanTidakBisaDitangguhkan' => $laporan->alasanTidakBisaDitangguhkan(),
            ]
        );
    }

    /**
     * Menyimpan tindak lanjut Admin terhadap laporan.
     */
    public function tindakLanjuti(Request $request, int $id)
    {
        $laporan = LaporanMasalah::findOrFail($id);

        $validated = $request->validate([
            'status_laporan' => [
                'required',
                Rule::in(LaporanMasalah::STATUS_TINDAK_LANJUT),
            ],

            'jenis_tindakan' => [
                'required',
                'string',
                'max:255',
            ],

            'aksi_penangguhan' => [
                'nullable',
                Rule::in([
                    'tidak_ada',
                    'tangguhkan',
                ]),
            ],

            // Diisi 1 oleh tombol "Ya, tangguhkan" di modal konfirmasi.
            'konfirmasi_penangguhan' => [
                'accepted_if:aksi_penangguhan,tangguhkan',
            ],

            'balasan_pelapor' => [
                'nullable',
                'string',
            ],
        ], [
            'status_laporan.required' => 'Status laporan wajib dipilih.',
            'status_laporan.in' => 'Status laporan tidak valid.',
            'jenis_tindakan.required' => 'Jenis tindakan wajib diisi.',
            'jenis_tindakan.max' => 'Jenis tindakan maksimal 255 karakter.',
            'aksi_penangguhan.in' => 'Pilihan tindakan penangguhan tidak valid.',
            'konfirmasi_penangguhan.accepted_if' => 'Konfirmasi penangguhan terlebih dahulu.',
        ]);

        $tangguhkan = ($validated['aksi_penangguhan'] ?? 'tidak_ada') === 'tangguhkan';

        if ($tangguhkan) {
            // Laporan yang tidak terbukti tidak boleh berujung penangguhan.
            if ($validated['status_laporan'] === LaporanMasalah::STATUS_DITOLAK) {
                throw ValidationException::withMessages([
                    'aksi_penangguhan' => 'Laporan yang ditolak tidak bisa sekaligus menangguhkan objek.',
                ]);
            }

            $alasan = $laporan->alasanTidakBisaDitangguhkan();

            if ($alasan !== null) {
                throw ValidationException::withMessages([
                    'aksi_penangguhan' => $alasan,
                ]);
            }
        }

        DB::transaction(function () use ($request, $laporan, $validated, $tangguhkan) {
            $objekDitangguhkan = null;
            $idObjekDitangguhkan = null;

            if ($tangguhkan) {
                $objek = $laporan->objekDilaporkan();

                if ($objek instanceof Listing) {
                    $objek->update([
                        'status_listing' => Listing::STATUS_DITANGGUHKAN,
                    ]);
                }

                if ($objek instanceof Pengguna) {
                    $objek->update([
                        'status_akun' => Pengguna::STATUS_DITANGGUHKAN,
                    ]);
                }

                $objekDitangguhkan = $laporan->jenis_objek;
                $idObjekDitangguhkan = $objek->getKey();
            }

            $laporan->update([
                'status_laporan' => $validated['status_laporan'],
            ]);

            TindakLanjut::create([
                'id_laporan_masalah' => $laporan->id_laporan_masalah,
                'id_admin' => $request->user()->id_pengguna,
                'jenis_tindakan' => $validated['jenis_tindakan'],
                'objek_ditangguhkan' => $objekDitangguhkan,
                'id_objek_ditangguhkan' => $idObjekDitangguhkan,
                'balasan_pelapor' => $validated['balasan_pelapor'] ?? null,
                'waktu_tindak_lanjut' => now(),
            ]);
        });

        return redirect()
            ->route('laporan-masalah.show', $laporan->id_laporan_masalah)
            ->with('sukses', 'Tindak lanjut laporan berhasil disimpan.');
    }
}
