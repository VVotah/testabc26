@extends('layouts.app')

@section('title', 'Kampanye')

@section('content')
<x-kepala-halaman judul="Kampanye penyelamatan makanan" />

@include('partials.pesan')

@if ($kampanye->isEmpty())
    <x-panel>
        <x-kosong judul="Belum ada kampanye" ikon="bendera">
            Kampanye yang sedang atau akan berjalan akan tampil di sini.
        </x-kosong>
    </x-panel>
@else
    <div class="row row-cols-1 row-cols-md-2 g-3">
        @foreach ($kampanye as $k)
            <div class="col">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="mb-2"><x-status :nilai="$k->status_kampanye" /></div>
                        <h2 class="card-title fs-5">{{ $k->nama_kampanye }}</h2>
                        <p class="card-text">{{ $k->deskripsi }}</p>
                        <dl class="fl-rincian mb-0">
                            <dt>Lokasi</dt>
                            <dd>{{ $k->lokasi }}</dd>
                            <dt>Tanggal</dt>
                            <dd class="fl-data">
                                {{ \App\Support\Waktu::tanggal($k->tanggal_mulai) }}
                                @if (! $k->tanggal_mulai->isSameDay($k->tanggal_selesai))
                                    - {{ \App\Support\Waktu::tanggal($k->tanggal_selesai) }}
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection