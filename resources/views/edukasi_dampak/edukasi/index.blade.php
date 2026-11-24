@extends('layouts.app')  

@section('title', 'Edukasi')

@section('content')
<x-kepala-halaman judul="Edukasi food waste" />

@include('partials.pesan')

@if ($artikel->isEmpty())
    <x-panel>
        <x-kosong judul="Belum ada artikel" ikon="buku">
            Artikel edukasi tentang food waste akan tampil di sini setelah diterbitkan.
        </x-kosong>
    </x-panel>
@else
    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3">
        @foreach ($artikel as $a)
            <div class="col">
                <div class="card h-100">
                    @if ($a->url_gambar)
                        <img src="{{ $a->url_gambar }}" class="card-img-top" alt="{{ $a->judul }}">
                    @endif
                    <div class="card-body d-flex flex-column">
                        <span class="fl-cap mb-1">{{ \App\Support\Waktu::tanggal($a->waktu_terbit) }}</span>
                        <h2 class="card-title fs-5">{{ $a->judul }}</h2>
                        <p class="card-text">{{ $a->cuplikan }}</p>
                        <div class="mt-auto">
                            <a href="{{ route('edukasi.show', $a) }}" class="btn btn-sekunder btn-sm">Baca selengkapnya</a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection