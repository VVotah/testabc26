@extends('layouts.app')

@section('title', 'Titik Pengambilan')

@section('content')
    <x-kepala-halaman judul="Titik Pengambilan"
        keterangan="Tempat penerima mengambil makanan. Titik dipilih saat kamu membuat listing.">
        @if ($daftarTitik->isNotEmpty())
            <a href="{{ route('titik-pengambilan.create') }}" class="btn btn-utama"><x-ikon nama="tambah" />Tambah titik</a>
        @endif
    </x-kepala-halaman>

    @include('partials.pesan')

    <x-panel :judul="'Daftar titik pengambilan'">
        @if ($daftarTitik->isEmpty())
            <x-kosong judul="Belum ada titik pengambilan" ikon="lokasi">
                Tambahkan minimal satu titik supaya bisa dipilih saat membuat listing makanan.
                <x-slot:aksi>
                    <a href="{{ route('titik-pengambilan.create') }}" class="btn btn-utama"><x-ikon nama="tambah" />Tambah titik pertama</a>
                </x-slot:aksi>
            </x-kosong>
        @else
            <x-slot:aksi><span class="fl-cap">{{ $daftarTitik->count() }} titik</span></x-slot:aksi>
            <ul class="fl-daftar">
                @foreach ($daftarTitik as $titik)
                    <li>
                        <span class="fl-daftar-gambar fl-daftar-ikon"><x-ikon nama="lokasi" /></span>
                        <div class="fl-daftar-isi">
                            <a href="{{ route('titik-pengambilan.show', $titik) }}" class="fl-daftar-judul fl-tautan-judul">{{ $titik->nama_titik }}</a>
                            <div class="fl-daftar-ket">{{ $titik->alamat }}</div>
                            @if ($titik->patokan)
                                <div class="fl-daftar-ket">Patokan: {{ $titik->patokan }}</div>
                            @endif
                        </div>
                        <div class="fl-daftar-aksi">
                            <a href="{{ route('titik-pengambilan.edit', $titik) }}" class="btn btn-sekunder btn-sm">Ubah</a>
                            <button type="button" class="btn btn-outline-danger btn-sm"
                                data-bs-toggle="modal" data-bs-target="#modalHapusTitik"
                                data-nama="{{ $titik->nama_titik }}"
                                data-aksi="{{ route('titik-pengambilan.destroy', $titik) }}">Hapus</button>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </x-panel>

    @include('akun_mitra.titik._modal-hapus')
@endsection
