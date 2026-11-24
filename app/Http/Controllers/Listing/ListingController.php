<?php

namespace App\Http\Controllers\Listing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Listing\SimpanListingRequest;
use App\Models\KategoriMakanan;
use App\Models\Listing;
use App\Models\Mitra;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class ListingController extends Controller
{
    /**
     * Menampilkan katalog listing aktif untuk penerima.
     */
    public function katalog(Request $request): View
    {
        $query = Listing::with([
            'kategori',
            'mitra',
            'titikPengambilan',
        ])
            ->where('status_listing', Listing::STATUS_AKTIF)
            ->where('jumlah_porsi', '>', 0)
            ->where('batas_kedaluwarsa', '>', now());

        // Pencarian berdasarkan nama makanan.
        if ($request->filled('cari')) {
            $query->where(
                'nama_makanan',
                'like',
                '%' . $request->cari . '%'
            );
        }

        // Filter berdasarkan kategori makanan.
        if ($request->filled('kategori')) {
            $query->where(
                'id_kategori_makanan',
                $request->kategori
            );
        }

        return view('listing.katalog', [
            'daftarListing' => $query
                ->latest('id_listing')
                ->get(),
            'daftarKategori' => KategoriMakanan::orderByRaw(
                'CASE nama_kategori
                    WHEN ? THEN 1
                    WHEN ? THEN 2
                    WHEN ? THEN 3
                    WHEN ? THEN 4
                    WHEN ? THEN 5
                    ELSE 6
                END',
                [
                    'Buah & sayur',
                    'Makanan berat',
                    'Minuman',
                    'Roti & kue',
                    'Lainnya',
                ]
            )->get(),
            'kataKunci' => $request->cari,
            'kategoriDipilih' => $request->kategori,
        ]);
    }

    /**
     * Menampilkan daftar listing milik mitra yang sedang login.
     */
    public function index(Request $request): View
    {
        $mitra = $this->mitra($request);

        return view('listing.index', [
            'daftarListing' => Listing::with([
                'titikPengambilan',
                'kategori',
            ])
                ->where('id_mitra', $mitra->id_mitra)
                ->latest('id_listing')
                ->get(),
        ]);
    }

    /**
     * Menampilkan form tambah listing.
     */
    public function create(Request $request): View|RedirectResponse
    {
        $mitra = $this->mitra($request);

        // Mitra harus memiliki minimal satu titik pengambilan.
        if (! $mitra->titikPengambilan()->exists()) {
            return redirect()
                ->route('titik-pengambilan.index')
                ->with(
                    'galat',
                    'Tambahkan minimal satu titik pengambilan dulu sebelum membuat listing.'
                );
        }

        return view('listing.form', $this->dataForm($mitra, new Listing));
    }

    /**
     * Menyimpan listing baru.
     */
    public function store(SimpanListingRequest $request): RedirectResponse
    {
        $mitra = $this->mitra($request);

        $data = $request->validated();

        // Listing Gratis tidak membutuhkan harga.
        $data['harga'] = $data['tipe_listing'] === Listing::TIPE_DISKON
            ? $data['harga']
            : null;

        $data['id_mitra'] = $mitra->id_mitra;
        $data['status_listing'] = Listing::STATUS_AKTIF;
        $data['foto'] = $request->file('foto')->store('listing', 'public');

        try {
            $listing = Listing::create($data);
        } catch (Throwable $e) {
            // Hapus foto jika penyimpanan listing gagal.
            Storage::disk('public')->delete($data['foto']);

            throw $e;
        }

        return redirect()
            ->route('listing.index')
            ->with(
                'sukses',
                "Listing \"{$listing->nama_makanan}\" berhasil dibuat dan berstatus Aktif."
            );
    }

    /**
     * Menampilkan detail listing milik mitra yang sedang login.
     */
    public function show(Request $request, Listing $listing): View
    {
        $mitra = $this->mitra($request);

        // Mitra hanya boleh melihat detail listing miliknya.
        abort_unless(
            $listing->id_mitra == $mitra->id_mitra,
            404
        );

        $listing->load([
            'titikPengambilan',
            'kategori',
        ]);

        return view('listing.show', compact('listing'));
    }

    /**
     * Menampilkan form edit listing milik mitra yang sedang login.
     */
    public function edit(Request $request, Listing $listing): View
    {
        $mitra = $this->mitra($request);

        // Mitra hanya boleh mengedit listing miliknya.
        abort_unless(
            $listing->id_mitra == $mitra->id_mitra,
            404
        );

        // Hanya listing aktif yang bisa diedit.
        abort_unless(
            $listing->status_listing === Listing::STATUS_AKTIF,
            403
        );

        return view('listing.form', $this->dataForm($mitra, $listing));
    }

    /**
     * Memperbarui listing milik mitra yang sedang login.
     */
    public function update(
        SimpanListingRequest $request,
        Listing $listing
    ): RedirectResponse {
        $mitra = $this->mitra($request);

        // Mitra hanya boleh memperbarui listing miliknya.
        abort_unless(
            $listing->id_mitra == $mitra->id_mitra,
            404
        );

        // Hanya listing aktif yang bisa diperbarui.
        abort_unless(
            $listing->status_listing === Listing::STATUS_AKTIF,
            403
        );

        $data = $request->validated();

        // Listing Gratis tidak membutuhkan harga.
        $data['harga'] = $data['tipe_listing'] === Listing::TIPE_DISKON
            ? $data['harga']
            : null;

        // Foto lama tetap digunakan jika tidak ada foto baru.
        if ($request->hasFile('foto')) {
            $fotoLama = $listing->foto;

            $data['foto'] = $request->file('foto')
                ->store('listing', 'public');
        }

        // Jika jumlah porsi menjadi 0, status listing berubah menjadi Habis.
        if ((int) $data['jumlah_porsi'] === 0) {
            $data['status_listing'] = Listing::STATUS_HABIS;
        }

        $listing->update($data);

        // Hapus foto lama setelah data berhasil diperbarui.
        if (isset($fotoLama)) {
            Storage::disk('public')->delete($fotoLama);
        }

        return redirect()
            ->route('listing.show', $listing)
            ->with(
                'sukses',
                "Listing \"{$listing->nama_makanan}\" berhasil diperbarui."
            );
    }

    /**
     * Menutup listing milik mitra yang sedang login.
     */
    public function close(Request $request, Listing $listing): RedirectResponse
    {
        $mitra = $this->mitra($request);

        // Mitra hanya boleh menutup listing miliknya.
        abort_unless(
            $listing->id_mitra == $mitra->id_mitra,
            404
        );

        // Hanya listing yang masih Aktif yang dapat ditutup.
        if ($listing->status_listing !== Listing::STATUS_AKTIF) {
            return redirect()
                ->route('listing.show', $listing)
                ->with('galat', 'Hanya listing berstatus Aktif yang dapat ditutup.');
        }

        $listing->update([
            'status_listing' => Listing::STATUS_DITUTUP,
        ]);

        return redirect()
            ->route('listing.show', $listing)
            ->with(
                'sukses',
                "Listing \"{$listing->nama_makanan}\" berhasil ditutup."
            );
    }

    /**
     * Menghapus listing milik mitra yang sedang login.
     */
    public function destroy(Request $request, Listing $listing): RedirectResponse
    {
        $mitra = $this->mitra($request);

        // Mitra hanya boleh menghapus listing miliknya.
        abort_unless(
            $listing->id_mitra == $mitra->id_mitra,
            404
        );

        $namaMakanan = $listing->nama_makanan;
        $foto = $listing->foto;

        // Hapus data listing dari database.
        $listing->delete();

        // Hapus foto setelah data berhasil dihapus.
        if ($foto) {
            Storage::disk('public')->delete($foto);
        }

        return redirect()
            ->route('listing.index')
            ->with(
                'sukses',
                "Listing \"{$namaMakanan}\" berhasil dihapus."
            );
    }

    /**
     * Menyiapkan data untuk form listing.
     */
    private function dataForm(Mitra $mitra, Listing $listing): array
    {
        return [
            'listing' => $listing,
            'daftarTitik' => $mitra->titikPengambilan()
                ->orderBy('nama_titik')
                ->get(),
            'daftarKategori' => KategoriMakanan::orderByRaw(
                'CASE nama_kategori
                    WHEN ? THEN 1
                    WHEN ? THEN 2
                    WHEN ? THEN 3
                    WHEN ? THEN 4
                    WHEN ? THEN 5
                    ELSE 6
                END',
                [
                    'Buah & sayur',
                    'Makanan berat',
                    'Minuman',
                    'Roti & kue',
                    'Lainnya',
                ]
            )->get(),
        ];
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
