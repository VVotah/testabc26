@extends('layouts.app')

@section('title', 'Kelola Artikel')

@section('content')
<x-kepala-halaman judul="Kelola Artikel Edukasi">
    <a href="{{ route('artikel.create') }}" class="btn btn-utama"><x-ikon nama="tambah" />Tulis artikel</a>
</x-kepala-halaman>

@include('partials.pesan')

<nav class="fl-saring mb-3">
    <a href="{{ route('artikel.kelola') }}" @class(['aktif' => ! $status]) @if (! $status) aria-current="page" @endif>
        Semua <span class="fl-saring-jumlah">{{ $jumlah->sum() }}</span>
    </a>
    @foreach (\App\Models\Artikel::STATUS as $s)
        <a href="{{ route('artikel.kelola', ['status' => $s]) }}" @class(['aktif' => $status === $s]) @if ($status === $s) aria-current="page" @endif>
            {{ $s }} <span class="fl-saring-jumlah">{{ $jumlah[$s] ?? 0 }}</span>
        </a>
    @endforeach
</nav>

<x-panel judul="Daftar artikel">
    @if ($artikel->isEmpty())
        <x-kosong judul="Belum ada artikel" ikon="buku">
            @if ($status)
                Tidak ada artikel berstatus {{ $status }}.
            @else
                Pilih Tulis artikel untuk membuat artikel edukasi pertama.
            @endif
        </x-kosong>
    @else
        <div class="fl-tabel">
            <table class="table">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Status</th>
                        <th>Penulis</th>
                        <th>Dibuat</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($artikel as $a)
                        <tr>
                            <td>{{ $a->judul }}</td>
                            <td><x-status :nilai="$a->status_artikel" /></td>
                            <td>{{ $a->penulis->nama ?? '-' }}</td>
                            <td class="fl-data">{{ \App\Support\Waktu::tanggal($a->waktu_dibuat) }}</td>
                            <td class="text-end">
                                @if ($a->bisaDiubah())
                                    <a href="{{ route('artikel.edit', $a) }}" class="btn btn-sekunder btn-sm">Ubah</a>
                                @endif
                                @if ($a->bisaDiterbitkan())
                                    <form action="{{ route('artikel.terbitkan', $a) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-utama btn-sm">Terbitkan</button>
                                    </form>
                                @endif
                                @if ($a->bisaDiarsipkan())
                                    <button type="button" class="btn btn-sekunder btn-sm"
                                        data-bs-toggle="modal" data-bs-target="#modalArsip"
                                        data-nama="{{ $a->judul }}"
                                        data-aksi="{{ route('artikel.arsipkan', $a) }}">Arsipkan</button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-panel>

<div class="modal fade" id="modalArsip" tabindex="-1" aria-labelledby="judulModalArsip" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" id="formArsip" class="modal-content">
            @csrf
            @method('PATCH')
            <div class="modal-header">
                <h2 class="modal-title fs-5" id="judulModalArsip">Arsipkan artikel?</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Artikel <strong id="namaArsip"></strong> tidak akan tampil di halaman edukasi, tetapi datanya tetap tersimpan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sekunder" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-utama">Arsipkan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('modalArsip').addEventListener('show.bs.modal', function (e) {
        var tombol = e.relatedTarget;
        document.getElementById('formArsip').action = tombol.dataset.aksi;
        document.getElementById('namaArsip').textContent = tombol.dataset.nama;
    });
</script>
@endpush