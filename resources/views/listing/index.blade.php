@extends('layouts.app')

@section('title', 'Daftar Listing Saya')

@section('content')
    <x-kepala-halaman
        judul="Daftar Listing Saya"
        keterangan="Makanan berlebih yang kamu bagikan ke penerima."
    >
        <a href="{{ route('listing.create') }}" class="btn btn-utama">
            <x-ikon nama="tambah" />
            Tambah listing
        </a>
    </x-kepala-halaman>

    @include('partials.pesan')

    <x-panel judul="Listing makanan">
        @if ($daftarListing->isEmpty())
            <x-kosong judul="Belum ada listing" ikon="kotak">
                Buat listing pertamamu supaya penerima bisa menemukan makanan
                yang kamu bagikan.

                <x-slot:aksi>
                    <a href="{{ route('listing.create') }}" class="btn btn-utama">
                        <x-ikon nama="tambah" />
                        Tambah listing
                    </a>
                </x-slot:aksi>
            </x-kosong>
        @else
            <div class="fl-tabel">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Makanan</th>
                            <th>Tipe &amp; harga</th>
                            <th>Sisa porsi</th>
                            <th>Batas kedaluwarsa</th>
                            <th>Titik pengambilan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($daftarListing as $listing)
                            <tr>
                                <td>
                                    {{ $listing->nama_makanan }}
                                    <div>
                                        <span class="fl-cap">
                                            {{ $listing->kategori?->nama_kategori }}
                                        </span>
                                    </div>
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
                                    {{ $listing->titikPengambilan?->nama_titik }}
                                </td>

                                <td>
                                    <x-status :nilai="$listing->status_listing" />
                                </td>

                                <td>
                                    <div class="d-flex gap-2">
                                        <a
                                            href="{{ route('listing.show', $listing) }}"
                                            class="btn btn-sekunder btn-sm"
                                        >
                                            <x-ikon nama="kotak" />
                                            Detail
                                        </a>

                                        @if ($listing->status_listing === \App\Models\Listing::STATUS_AKTIF)
                                            <a
                                                href="{{ route('listing.edit', $listing) }}"
                                                class="btn btn-sekunder btn-sm"
                                            >
                                                Edit
                                            </a>
                                        @endif
                                    </div>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-panel>
@endsection
