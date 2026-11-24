<?php

namespace App\Http\Controllers\EdukasiDampak;

use App\Http\Controllers\Controller;
use App\Models\Kampanye;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KampanyeController extends Controller
{
    // R: daftar kampanye beserta statusnya (Admin)
    public function index(Request $request)
    {
        Kampanye::sinkronStatus();

        $status = $request->query('status');
        if (! in_array($status, Kampanye::STATUS, true)) {
            $status = null;
        }

        $kampanye = Kampanye::when($status, fn ($q) => $q->where('status_kampanye', $status))
            ->orderByDesc('tanggal_mulai')
            ->get();

        $jumlah = Kampanye::selectRaw('status_kampanye, count(*) as total')
            ->groupBy('status_kampanye')
            ->pluck('total', 'status_kampanye');

        return view('edukasi_dampak.kampanye.index', compact('kampanye', 'status', 'jumlah'));
    }

    // C: buat kampanye atau event
    public function create()
    {
        return view('edukasi_dampak.kampanye.create');
    }

    public function store(Request $request)
    {
        $data = $this->validasi($request);
        $data['id_pembuat'] = Auth::id();
        $data['status_kampanye'] = Kampanye::AKAN_DATANG;

        Kampanye::create($data);
        Kampanye::sinkronStatus();

        return redirect()->route('kampanye.kelola')->with('sukses', 'Kampanye berhasil dibuat.');
    }

    // U: ubah informasi kampanye
    public function edit(Kampanye $kampanye)
    {
        if (! $kampanye->bisaDiubah()) {
            return redirect()->route('kampanye.kelola')
                ->with('galat', 'Kampanye yang sudah selesai atau dibatalkan tidak dapat diubah.');
        }

        return view('edukasi_dampak.kampanye.edit', compact('kampanye'));
    }

    public function update(Request $request, Kampanye $kampanye)
    {
        if (! $kampanye->bisaDiubah()) {
            return redirect()->route('kampanye.kelola')
                ->with('galat', 'Kampanye yang sudah selesai atau dibatalkan tidak dapat diubah.');
        }

        $kampanye->update($this->validasi($request));
        Kampanye::sinkronStatus();

        return redirect()->route('kampanye.kelola')->with('sukses', 'Perubahan kampanye disimpan.');
    }

    // U: batalkan kampanye (status Dibatalkan, tidak ada hapus)
    public function batalkan(Kampanye $kampanye)
    {
        if (! $kampanye->bisaDibatalkan()) {
            return redirect()->route('kampanye.kelola')
                ->with('galat', 'Kampanye ini tidak dapat dibatalkan.');
        }

        $kampanye->batalkan();

        return redirect()->route('kampanye.kelola')->with('sukses', 'Kampanye dibatalkan.');
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'nama_kampanye' => ['required', 'string', 'max:150'],
            'deskripsi' => ['required', 'string'],
            'lokasi' => ['required', 'string', 'max:255'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
        ], [
            'nama_kampanye.required' => 'Nama kampanye wajib diisi.',
            'nama_kampanye.max' => 'Nama kampanye maksimal 150 karakter.',
            'deskripsi.required' => 'Deskripsi wajib diisi.',
            'lokasi.required' => 'Lokasi wajib diisi.',
            'lokasi.max' => 'Lokasi maksimal 255 karakter.',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi.',
            'tanggal_mulai.date' => 'Tanggal mulai tidak valid.',
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai.date' => 'Tanggal selesai tidak valid.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
        ]);
    }
}