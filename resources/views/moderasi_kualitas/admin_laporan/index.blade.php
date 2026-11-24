@extends('layouts.app')

@section('title', 'Laporan Masuk')

@php use App\Support\Waktu; @endphp

@section('content')
    <x-kepala-halaman judul="Laporan Masalah"
        keterangan="Tinjau laporan dari pengguna, lalu tentukan tindak lanjutnya." />

    @include('partials.pesan')

    <x-panel>
        @if ($laporan->isEmpty())
            <x-kosong judul="Belum ada laporan" ikon="bendera">
                Laporan masalah yang dikirim pengguna akan tampil di sini untuk ditinjau.
            </x-kosong>
        @else
            <div class="fl-tabel">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Objek dilaporkan</th>
                            <th>Kategori</th>
                            <th>Pelapor</th>
                            <th>Dilaporkan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($laporan as $item)
                            <tr>
                                <td>
                                    <a href="{{ route('laporan-masalah.show', $item->id_laporan_masalah) }}"
                                        class="fl-daftar-judul fl-tautan-judul">{{ $item->nama_objek }}</a>
                                    <div class="fl-cap">{{ $item->jenis_objek }}</div>
                                </td>
                                <td>{{ $item->kategori_masalah }}</td>
                                <td>{{ $item->pelapor?->nama ?? 'Pengguna tidak ditemukan' }}</td>
                                <td class="fl-data">{{ $item->waktu_laporan ? Waktu::lengkap($item->waktu_laporan) : '-' }}</td>
                                <td><x-status :nilai="$item->status_laporan" /></td>
                                <td>
                                    <a href="{{ route('laporan-masalah.show', $item->id_laporan_masalah) }}"
                                        class="btn btn-sekunder btn-sm">Tinjau</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-panel>

    @if ($laporan->hasPages())
        <div class="mt-3">{{ $laporan->links() }}</div>
    @endif
@endsection
