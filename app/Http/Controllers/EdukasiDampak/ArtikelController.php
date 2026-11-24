<?php

namespace App\Http\Controllers\EdukasiDampak;

use App\Http\Controllers\Controller;
use App\Models\Artikel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class ArtikelController extends Controller
{
    // R: daftar artikel beserta statusnya (Admin)
    public function index(Request $request)
    {
        $status = $request->query('status');
        if (! in_array($status, Artikel::STATUS, true)) {
            $status = null;
        }

        $artikel = Artikel::with('penulis')
            ->when($status, fn ($q) => $q->where('status_artikel', $status))
            ->orderByDesc('waktu_dibuat')
            ->get();

        $jumlah = Artikel::selectRaw('status_artikel, count(*) as total')
            ->groupBy('status_artikel')
            ->pluck('total', 'status_artikel');

        return view('edukasi_dampak.artikel.index', compact('artikel', 'status', 'jumlah'));
    }

    // C: tulis artikel
    public function create()
    {
        return view('edukasi_dampak.artikel.create');
    }

    // C: simpan sebagai Draf
    public function store(Request $request)
    {
        $data = $this->validasi($request);

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('artikel', 'public');
        }

        $data['id_penulis'] = Auth::id();
        $data['status_artikel'] = Artikel::DRAF;

        Artikel::create($data);

        return redirect()->route('artikel.kelola')->with('sukses', 'Artikel disimpan sebagai Draf.');
    }

    // U: ubah isi artikel
    public function edit(Artikel $artikel)
    {
        if (! $artikel->bisaDiubah()) {
            return redirect()->route('artikel.kelola')
                ->with('galat', 'Artikel yang sudah diarsipkan tidak dapat diubah.');
        }

        return view('edukasi_dampak.artikel.edit', compact('artikel'));
    }

    public function update(Request $request, Artikel $artikel)
    {
        if (! $artikel->bisaDiubah()) {
            return redirect()->route('artikel.kelola')
                ->with('galat', 'Artikel yang sudah diarsipkan tidak dapat diubah.');
        }

        $data = $this->validasi($request, $artikel);

        if ($request->hasFile('gambar')) {
            if ($artikel->gambar) {
                Storage::disk('public')->delete($artikel->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('artikel', 'public');
        } else {
            unset($data['gambar']);
        }

        $artikel->update($data);

        // U: Draf menjadi Terbit lewat tombol Terbitkan di formulir ubah
        if ($request->input('aksi') === 'terbit' && $artikel->bisaDiterbitkan()) {
            $artikel->terbitkan();

            return redirect()->route('artikel.kelola')->with('sukses', 'Artikel berhasil diterbitkan.');
        }

        return redirect()->route('artikel.kelola')->with('sukses', 'Perubahan artikel disimpan.');
    }

    // U: Draf menjadi Terbit (dari daftar)
    public function terbitkan(Artikel $artikel)
    {
        if (! $artikel->bisaDiterbitkan()) {
            return redirect()->route('artikel.kelola')
                ->with('galat', 'Hanya artikel berstatus Draf yang dapat diterbitkan.');
        }

        if (blank($artikel->judul) || blank($artikel->isi)) {
            return redirect()->route('artikel.edit', $artikel)
                ->with('galat', 'Judul dan isi wajib terisi sebelum artikel diterbitkan.');
        }

        $artikel->terbitkan();

        return redirect()->route('artikel.kelola')->with('sukses', 'Artikel berhasil diterbitkan.');
    }

    // U: Terbit menjadi Diarsipkan (tidak ada hapus)
    public function arsipkan(Artikel $artikel)
    {
        if (! $artikel->bisaDiarsipkan()) {
            return redirect()->route('artikel.kelola')
                ->with('galat', 'Hanya artikel berstatus Terbit yang dapat diarsipkan.');
        }

        $artikel->arsipkan();

        return redirect()->route('artikel.kelola')->with('sukses', 'Artikel diarsipkan.');
    }

    private function validasi(Request $request, ?Artikel $artikel = null): array
    {
        // Isi wajib saat diterbitkan atau saat mengubah artikel yang sudah Terbit (ASUMSI)
        $perluIsi = $request->input('aksi') === 'terbit'
            || ($artikel && $artikel->status_artikel === Artikel::TERBIT);

        return $request->validate([
            'judul' => ['required', 'string', 'max:150'],
            'isi' => [Rule::requiredIf($perluIsi), 'nullable', 'string'],
            'gambar' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ], [
            'judul.required' => 'Judul wajib diisi.',
            'judul.max' => 'Judul maksimal 150 karakter.',
            'isi.required' => 'Isi artikel wajib diisi sebelum diterbitkan.',
            'gambar.image' => 'Berkas harus berupa gambar.',
            'gambar.mimes' => 'Gambar harus berformat JPG atau PNG.',
            'gambar.max' => 'Ukuran gambar maksimal 5 MB.',
        ]);
    }
}