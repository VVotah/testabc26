@extends('layouts.app')

@section('title', 'Katalog Makanan')

@section('content')
    <x-kepala-halaman
        judul="Katalog Makanan"
        keterangan="Temukan makanan berlebih yang tersedia dari mitra FoodLoop."
    />

    <x-panel judul="Cari makanan" class="mb-4">
        <form
            action="{{ route('cari.index') }}"
            method="GET"
        >
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label for="cari" class="form-label">
                        Nama makanan
                    </label>

                    <input
                        type="text"
                        name="cari"
                        id="cari"
                        class="form-control"
                        placeholder="Cari nama makanan..."
                        value="{{ $kataKunci }}"
                    >
                </div>

                <div class="col-md-4">
                    <label for="kategori" class="form-label">
                        Kategori makanan
                    </label>

                    <select
                        name="kategori"
                        id="kategori"
                        class="form-select"
                    >
                        <option value="">Semua kategori</option>

                        @foreach ($daftarKategori as $kategori)
                            <option
                                value="{{ $kategori->id_kategori_makanan }}"
                                @selected(
                                    (string) $kategoriDipilih ===
                                    (string) $kategori->id_kategori_makanan
                                )
                            >
                                {{ $kategori->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-utama">
                        Cari
                    </button>

                    <a
                        href="{{ route('cari.index') }}"
                        class="btn btn-sekunder"
                    >
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </x-panel>

    <x-panel judul="Makanan tersedia">
        @if ($daftarListing->isEmpty())
            <x-kosong
                judul="Makanan tidak ditemukan"
                ikon="kotak"
            >
                Coba gunakan kata kunci atau kategori yang berbeda.
            </x-kosong>
        @else
            <div class="fl-tabel">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Makanan</th>
                            <th>Kategori</th>
                            <th>Tipe &amp; harga</th>
                            <th>Sisa porsi</th>
                            <th>Batas kedaluwarsa</th>
                            <th>Titik pengambilan</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($daftarListing as $listing)
                            <tr>
                                <td>
                                    {{ $listing->nama_makanan }}
                                </td>

                                <td>
                                    {{ $listing->kategori?->nama_kategori ?? '-' }}
                                </td>

                                <td class="fl-data">
                                    {{ $listing->tipe_harga_teks }}
                                </td>

                                <td class="fl-data">
                                    {{ $listing->jumlah_porsi }}
                                </td>

                                <td class="fl-data">
                                    {{ \App\Support\Waktu::lengkap($listing->batas_kedaluwarsa) }}
                                </td>

                                <td>
                                    {{ $listing->titikPengambilan?->nama_titik ?? '-' }}
                                </td>

                                <td>
                                    <x-status :nilai="$listing->status_listing" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-panel>
@endsection
