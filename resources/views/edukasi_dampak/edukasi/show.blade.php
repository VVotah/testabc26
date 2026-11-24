@extends('layouts.app')

@section('title', $artikel->judul)

@section('content')
<x-kepala-halaman :judul="$artikel->judul" :kembali="route('edukasi.index')" teks-kembali="Kembali ke daftar artikel" />

@include('partials.pesan')

<x-panel class="fl-kartu-isi">
    @if ($artikel->url_gambar)
        <img src="{{ $artikel->url_gambar }}" alt="{{ $artikel->judul }}" class="fl-pratinjau mb-3">
    @endif
    <p>
        <span class="fl-cap">{{ \App\Support\Waktu::tanggal($artikel->waktu_terbit) }}</span>
        @if ($artikel->penulis)
            <span class="fl-cap"> · {{ $artikel->penulis->nama }}</span>
        @endif
    </p>
    <div>{!! nl2br(e($artikel->isi)) !!}</div>
</x-panel>
@endsection