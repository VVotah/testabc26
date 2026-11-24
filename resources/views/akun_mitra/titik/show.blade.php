@extends('layouts.app')

@section('title', $titik->nama_titik)

@section('content')
    <x-kepala-halaman :judul="$titik->nama_titik" :kembali="route('titik-pengambilan.index')" teks-kembali="Kembali ke daftar titik">
        <a href="{{ route('titik-pengambilan.edit', $titik) }}" class="btn btn-sekunder">Ubah</a>
        <button type="button" class="btn btn-outline-danger"
            data-bs-toggle="modal" data-bs-target="#modalHapusTitik"
            data-nama="{{ $titik->nama_titik }}"
            data-aksi="{{ route('titik-pengambilan.destroy', $titik) }}">Hapus</button>
    </x-kepala-halaman>

    @include('partials.pesan')

    <x-panel class="fl-kartu-isi">
        <dl class="fl-rincian">
            <dt>Alamat</dt>
            <dd>{{ $titik->alamat }}</dd>
            <dt>Patokan</dt>
            <dd>{{ $titik->patokan ?: '-' }}</dd>
            <dt>Koordinat</dt>
            <dd>
                <span class="fl-data">{{ $titik->koordinat }}</span>
                <a href="{{ $titik->url_peta }}" target="_blank" rel="noopener" class="fl-tautan-peta">
                    <x-ikon nama="lokasi" />Lihat di OpenStreetMap
                </a>
            </dd>
            <dt>Dipakai listing aktif</dt>
            <dd><span class="fl-data">{{ $dipakaiListing }}</span> listing</dd>
        </dl>
    </x-panel>

    @include('akun_mitra.titik._modal-hapus')
@endsection
