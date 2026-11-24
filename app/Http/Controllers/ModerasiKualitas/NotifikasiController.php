<?php

namespace App\Http\Controllers\ModerasiKualitas;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;

class NotifikasiController extends Controller
{
    /**
     * Menampilkan daftar notifikasi pengguna yang sedang login.
     */
    public function index()
    {
        $notifikasi = Notifikasi::where('id_pengguna', auth()->id())
            ->orderByDesc('waktu_notifikasi')
            ->get();

        return view('moderasi_kualitas.notifikasi.index', compact('notifikasi'));
    }

    /**
     * Menampilkan detail notifikasi milik pengguna yang login.
     */
    public function show(int $id)
    {
        $notifikasi = Notifikasi::where('id_pengguna', auth()->id())
            ->where('id_notifikasi', $id)
            ->firstOrFail();

        return view('moderasi_kualitas.notifikasi.show', compact('notifikasi'));
    }

    /**
     * Menghapus notifikasi milik pengguna yang login.
     */
    public function destroy(int $id)
    {
        $notifikasi = Notifikasi::where('id_pengguna', auth()->id())
            ->where('id_notifikasi', $id)
            ->firstOrFail();

        $notifikasi->delete();

        return redirect()->route('notifikasi.index')
            ->with('sukses', 'Notifikasi berhasil dihapus.');
    }
}
