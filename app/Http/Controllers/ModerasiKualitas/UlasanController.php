<?php

namespace App\Http\Controllers\ModerasiKualitas;

use App\Http\Controllers\Controller;
use App\Models\JadwalPickup;
use App\Models\Peran;
use App\Models\Ulasan;
use Illuminate\Http\Request;

class UlasanController extends Controller
{
    /**
     * Memastikan hanya Penerima yang dapat mengakses fitur ulasan.
     */
    private function pastikanPenerima(): void
    {
        abort_unless(
            auth()->user()->punyaPeran(Peran::PENERIMA),
            403
        );
    }

    /**
     * Menampilkan pickup selesai yang belum diberi ulasan.
     */
    public function index()
    {
        $this->pastikanPenerima();

        $pickup = JadwalPickup::with([
            'permintaan.listing.mitra.pengguna',
        ])
            ->where('status_pickup', 'Selesai')
            ->whereHas('permintaan', function ($query) {
                $query->where('id_penerima', auth()->id());
            })
            ->whereNotIn(
                'id_jadwal_pickup',
                Ulasan::select('id_jadwal_pickup')
            )
            ->orderByDesc('waktu_pickup')
            ->get();

        return view('moderasi_kualitas.ulasan.index', compact('pickup'));
    }

    /**
     * Menampilkan formulir ulasan untuk pickup tertentu.
     */
    public function create(int $id)
    {
        $this->pastikanPenerima();

        $pickup = JadwalPickup::with([
            'permintaan.listing.mitra.pengguna',
        ])
            ->where('id_jadwal_pickup', $id)
            ->where('status_pickup', 'Selesai')
            ->whereHas('permintaan', function ($query) {
                $query->where('id_penerima', auth()->id());
            })
            ->firstOrFail();

        if (Ulasan::where('id_jadwal_pickup', $id)->exists()) {
            return redirect()->route('ulasan.index')
                ->with('galat', 'Pickup ini sudah memiliki ulasan.');
        }

        return view('moderasi_kualitas.ulasan.create', compact('pickup'));
    }

    /**
     * Menyimpan ulasan dari Penerima untuk Donatur.
     */
    public function store(Request $request, int $id)
    {
        $this->pastikanPenerima();

        $pickup = JadwalPickup::with([
            'permintaan.listing.mitra',
        ])
            ->where('id_jadwal_pickup', $id)
            ->where('status_pickup', 'Selesai')
            ->whereHas('permintaan', function ($query) {
                $query->where('id_penerima', auth()->id());
            })
            ->firstOrFail();

        if (Ulasan::where('id_jadwal_pickup', $id)->exists()) {
            return redirect()->route('ulasan.index')
                ->with('galat', 'Pickup ini sudah memiliki ulasan.');
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'teks_ulasan' => ['nullable', 'string'],
        ]);

        $donatur = $pickup->permintaan->listing->mitra->id_pengguna;

        Ulasan::create([
            'id_penerima' => auth()->id(),
            'id_donatur' => $donatur,
            'id_jadwal_pickup' => $pickup->id_jadwal_pickup,
            'rating' => $validated['rating'],
            'teks_ulasan' => $validated['teks_ulasan'] ?? null,
        ]);

        return redirect()->route('ulasan.index')
            ->with('sukses', 'Ulasan berhasil dikirim.');
    }
}