@extends('layouts.app')

@section('title', 'Verifikasi Mitra')

@php
    $kosong = [
        'menunggu' => ['Tidak ada pengajuan yang menunggu', 'Pengajuan mitra baru akan muncul di sini untuk ditinjau.'],
        'terverifikasi' => ['Belum ada mitra yang disetujui', 'Mitra yang kamu setujui akan tercatat di sini.'],
        'ditolak' => ['Belum ada pengajuan yang ditolak', 'Pengajuan yang kamu tolak akan tercatat di sini beserta alasannya.'],
    ][$saring];
@endphp

@section('content')
    <x-kepala-halaman judul="Verifikasi Mitra"
        keterangan="Tinjau data usaha dan dokumen calon mitra sebelum mereka bisa membuat listing." />

    @include('partials.pesan')

    <nav class="fl-saring mb-3" aria-label="Saring status pengajuan">
        @foreach ($label as $kunci => $teks)
            <a href="{{ route('verifikasi-mitra.index', ['status' => $kunci]) }}"
                @class(['aktif' => $saring === $kunci]) @if ($saring === $kunci) aria-current="page" @endif>
                {{ $teks }} <span class="fl-saring-jumlah">{{ $jumlah[$kunci] }}</span>
            </a>
        @endforeach
    </nav>

    <x-panel>
        @if ($pengajuan->isEmpty())
            <x-kosong :judul="$kosong[0]" ikon="perisai">{{ $kosong[1] }}</x-kosong>
        @else
            <ul class="fl-daftar">
                @foreach ($pengajuan as $item)
                    <li>
                        <span class="fl-daftar-gambar fl-daftar-ikon"><x-ikon nama="toko" /></span>
                        <div class="fl-daftar-isi">
                            <div class="fl-daftar-judul">{{ $item->mitra->nama_mitra }}</div>
                            <div class="fl-daftar-ket">
                                {{ $item->mitra->jenis_mitra }} &middot; {{ $item->mitra->kota }} &middot;
                                @if ($item->sudahDiputuskan())
                                    diputuskan {{ \App\Support\Waktu::relatif($item->tanggal_keputusan) }}
                                @else
                                    diajukan {{ \App\Support\Waktu::relatif($item->tanggal_pengajuan) }}
                                @endif
                            </div>
                        </div>
                        <div class="fl-daftar-aksi">
                            <x-status :nilai="$item->status" />
                            <a href="{{ route('verifikasi-mitra.show', $item) }}"
                                class="btn btn-sm {{ $item->sudahDiputuskan() ? 'btn-sekunder' : 'btn-utama' }}">
                                {{ $item->sudahDiputuskan() ? 'Lihat' : 'Tinjau' }}
                            </a>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </x-panel>

    @if ($pengajuan->hasPages())
        <div class="mt-3">{{ $pengajuan->links() }}</div>
    @endif
@endsection
