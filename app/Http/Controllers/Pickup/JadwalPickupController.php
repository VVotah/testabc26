<?php

namespace App\Http\Controllers\Pickup;

use App\Http\Controllers\Controller;
use App\Models\JadwalPickup;
use App\Models\KonfirmasiSerahTerima;
use App\Models\TitikPengambilan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class JadwalPickupController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $titikList = TitikPengambilan::with('mitra')
            ->orderBy('nama_titik')
            ->get();

        $query = JadwalPickup::with(['titikPengambilan', 'donatur', 'penerima', 'konfirmasiSerahTerima'])
            ->latest('id_jadwal_pickup');

        $namaPeran = $user->peran?->nama_peran;
        if ($namaPeran === 'Mitra/Donatur') {
            $query->where('id_donatur', $user->id_pengguna);
        } elseif ($namaPeran === 'Penerima') {
            $query->where('id_penerima', $user->id_pengguna);
        }

        $jadwals = $query->get();

        return view('pickup.index', compact('jadwals', 'titikList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => ['required', 'date', 'after_or_equal:today'],
            'jam' => ['required', 'date_format:H:i'],
            'id_titik' => ['required', 'integer', Rule::exists('titik_pengambilan', 'id_titik')],
        ], [
            'required' => 'Kolom ini wajib diisi.',
            'tanggal.date' => 'Format tanggal tidak valid.',
            'tanggal.after_or_equal' => 'Tanggal pengambilan tidak boleh sebelum hari ini.',
            'jam.date_format' => 'Pilih slot waktu yang tersedia.',
            'id_titik.exists' => 'Titik pengambilan tidak ditemukan.',
            'id_titik.integer' => 'Titik pengambilan tidak ditemukan.',
        ]);

        $titik = TitikPengambilan::with('mitra')->findOrFail($validated['id_titik']);

        $idDonatur = $titik->mitra->id_pengguna;
        $idPenerima = auth()->id();
        $slotWaktu = $validated['tanggal'].' '.$validated['jam'].':00';

        $kodePickup = DB::transaction(function () use ($idDonatur, $idPenerima, $slotWaktu, $titik) {
            $jadwal = JadwalPickup::create([
                'id_donatur' => $idDonatur,
                'id_penerima' => $idPenerima,
                'id_permintaan' => null,
                'slot_waktu' => $slotWaktu,
                'id_titik' => $titik->id_titik,
                'status_jadwal' => 'Menunggu',
            ]);

            $kode = $this->buatKodePickupUnik();

            KonfirmasiSerahTerima::create([
                'id_jadwal_pickup' => $jadwal->id_jadwal_pickup,
                'kode_pickup' => $kode,
                'status_serah_terima' => 'Menunggu',
            ]);

            return $kode;
        });

        return redirect()
            ->route('pickup.index')
            ->with('sukses', "Jadwal pickup berhasil diajukan (Kode Pickup: {$kodePickup}). Menunggu persetujuan donatur.");
    }

    public function setujui(JadwalPickup $jadwal)
    {
        if ((int) $jadwal->id_donatur !== (int) auth()->id()) {
            return back()->with('galat', 'Anda tidak memiliki akses untuk menyetujui jadwal pickup ini.');
        }

        if ($jadwal->status_jadwal !== 'Menunggu') {
            return back()->with('galat', 'Hanya jadwal berstatus Menunggu yang dapat disetujui.');
        }

        DB::transaction(function () use ($jadwal) {
            $jadwal->update([
                'status_jadwal' => 'Disetujui',
            ]);

            if (! $jadwal->konfirmasiSerahTerima) {
                KonfirmasiSerahTerima::create([
                    'id_jadwal_pickup' => $jadwal->id_jadwal_pickup,
                    'kode_pickup' => $this->buatKodePickupUnik(),
                    'status_serah_terima' => 'Menunggu',
                ]);
            }
        });

        return redirect()
            ->route('pickup.index')
            ->with('sukses', 'Jadwal pickup berhasil disetujui.');
    }

    public function batalkan(JadwalPickup $jadwal)
    {
        $userId = (int) auth()->id();

        if ((int) $jadwal->id_donatur !== $userId && (int) $jadwal->id_penerima !== $userId) {
            return back()->with('galat', 'Anda tidak memiliki akses untuk membatalkan jadwal pickup ini.');
        }

        if (! in_array($jadwal->status_jadwal, ['Menunggu', 'Disetujui', 'Dijadwalkan'], true)) {
            return back()->with('galat', 'Jadwal pickup ini sudah tidak dapat dibatalkan.');
        }

        // Penerima hanya boleh membatalkan jadwal yang belum disetujui donatur.
        if ((int) $jadwal->id_penerima === $userId && $jadwal->status_jadwal !== 'Menunggu') {
            return back()->with('galat', 'Jadwal yang sudah disetujui donatur tidak dapat dibatalkan.');
        }

        $jadwal->update([
            'status_jadwal' => 'Dibatalkan',
        ]);

        return redirect()
            ->route('pickup.index')
            ->with('sukses', 'Jadwal pickup telah dibatalkan.');
    }

    public function konfirmasi()
    {
        $user = auth()->user();

        $daftarKonfirmasi = KonfirmasiSerahTerima::with(['jadwalPickup.titikPengambilan', 'jadwalPickup.penerima'])
            ->whereHas('jadwalPickup', fn ($q) => $q->where('id_donatur', $user->id_pengguna))
            ->latest('id_konfirmasi')
            ->get();

        return view('pickup.konfirmasi', compact('daftarKonfirmasi'));
    }

    public function storeKonfirmasi(Request $request)
    {
        $validated = $request->validate([
            'kode_pickup' => ['required', 'string', 'max:32'],
        ], [
            'kode_pickup.required' => 'Masukkan kode pickup.',
            'kode_pickup.max' => 'Kode pickup maksimal 32 karakter.',
        ]);

        $user = auth()->user();
        $kode = strtoupper(trim($validated['kode_pickup']));

        $konfirmasi = KonfirmasiSerahTerima::with('jadwalPickup')
            ->where('kode_pickup', $kode)
            ->first();

        if (! $konfirmasi || ! $konfirmasi->jadwalPickup || (int) $konfirmasi->jadwalPickup->id_donatur !== (int) $user->id_pengguna) {
            return back()
                ->withInput()
                ->with('galat', 'Kode pickup tidak valid atau bukan milik jadwal pickup Anda.');
        }

        if ($konfirmasi->jadwalPickup->status_jadwal === 'Menunggu') {
            return back()
                ->withInput()
                ->with('galat', 'Jadwal pickup belum disetujui. Silakan setujui jadwal terlebih dahulu di halaman Jadwal Pickup.');
        }

        if ($konfirmasi->jadwalPickup->status_jadwal === 'Dibatalkan') {
            return back()
                ->withInput()
                ->with('galat', 'Jadwal pickup ini sudah dibatalkan sehingga kode tidak dapat dikonfirmasi.');
        }

        if ($konfirmasi->status_serah_terima !== 'Menunggu') {
            return back()
                ->withInput()
                ->with('galat', 'Kode pickup ini sudah pernah diverifikasi atau tidak lagi berstatus Menunggu.');
        }

        DB::transaction(function () use ($konfirmasi) {
            $konfirmasi->update([
                'status_serah_terima' => 'Berhasil',
            ]);

            $konfirmasi->jadwalPickup->update([
                'status_jadwal' => 'Selesai',
            ]);
        });

        return redirect()
            ->route('pickup.konfirmasi')
            ->with('sukses', 'Konfirmasi serah terima berhasil! Status jadwal pickup telah diperbarui menjadi Selesai.');
    }

    private function buatKodePickupUnik(): string
    {
        do {
            $kode = 'FL-'.random_int(1000, 9999);
        } while (KonfirmasiSerahTerima::where('kode_pickup', $kode)->exists());

        return $kode;
    }
}
