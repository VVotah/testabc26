@extends('layouts.app')

@section('title', $judul)

@section('content')
    <x-kepala-halaman :judul="$judul" :keterangan="'Halo, '.auth()->user()->nama.'. Ini ringkasan hari ini.'" />

    @include('partials.pesan')

    @if ($peran === \App\Models\Peran::MITRA)
        {{-- Langkah terpenting Mitra: data usaha harus lengkap dan terverifikasi sebelum bisa membuat listing. --}}
        <x-panel class="fl-langkah mb-4">
            <div class="fl-langkah-isi">
                <span class="fl-cap">Status verifikasi akun</span>
                @if ($statusMitra)
                    <x-status :nilai="$statusMitra" />
                @else
                    <span class="fw-bold">Data usaha belum diisi</span>
                @endif
            </div>
            @unless ($statusMitra === 'Terverifikasi')
                <p class="fl-langkah-teks">
                    @if ($statusMitra === 'Menunggu verifikasi')
                        Data usahamu sedang ditinjau Admin. Kamu bisa membuat listing setelah terverifikasi.
                    @elseif ($statusMitra === 'Ditolak')
                        Pengajuanmu ditolak Admin{{ $alasanDitolak ? ' dengan alasan: '.rtrim($alasanDitolak, '. ') : '' }}.
                        Perbaiki data usahamu di Profil Mitra, lalu ajukan verifikasi ulang.
                    @elseif ($statusMitra === 'Ditangguhkan')
                        Akun mitramu sedang ditangguhkan Admin. Hubungi Admin FoodLoop untuk informasi lebih lanjut.
                    @else
                        Data usaha akun ini belum ada. Hubungi Admin FoodLoop.
                    @endif
                </p>
            @endunless
            @if ($statusMitra === 'Ditolak')
                <a href="{{ route('profil-mitra.edit') }}" class="btn btn-aksen btn-sm">Perbaiki dan ajukan ulang</a>
            @endif
        </x-panel>
    @endif

    <div class="fl-statistik-baris mb-4">
        @foreach ($statistik as [$label, $angka, $ikon])
            <x-statistik :label="$label" :angka="$angka" :ikon="$ikon" />
        @endforeach
    </div>

    <div class="row g-4">
        @foreach ($panel as $isiPanel)
            @php
                [$judulPanel, $ikonPanel, $kosong] = $isiPanel;
                $isi = $isiPanel[3] ?? null;
                $adaIsi = $isi && collect($isi['data'])->first()?->isNotEmpty();
            @endphp
            <div class="col-12 col-xl-6">
                <x-panel :judul="$judulPanel" class="h-100">
                    @if ($isi && $adaIsi)
                        <x-slot:aksi><a href="{{ $isi['aksi'][1] }}" class="btn btn-sekunder btn-sm">{{ $isi['aksi'][0] }}</a></x-slot:aksi>
                        @include($isi['view'], $isi['data'])
                    @else
                        <x-kosong :judul="$kosong['judul']" :ikon="$ikonPanel">{{ $kosong['teks'] }}</x-kosong>
                    @endif
                </x-panel>
            </div>
        @endforeach
    </div>
@endsection
