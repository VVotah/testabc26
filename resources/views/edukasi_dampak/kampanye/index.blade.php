@extends('layouts.app')

@section('title', 'Kelola Kampanye')

@section('content')
<x-kepala-halaman judul="Kelola Kampanye">
    <a href="{{ route('kampanye.create') }}" class="btn btn-utama"><x-ikon nama="tambah" />Buat kampanye</a>
</x-kepala-halaman>

@include('partials.pesan')

<nav class="fl-saring mb-3">
    <a href="{{ route('kampanye.kelola') }}" @class(['aktif' => ! $status]) @if (! $status) aria-current="page" @endif>
        Semua <span class="fl-saring-jumlah">{{ $jumlah->sum() }}</span>
    </a>
    @foreach (\App\Models\Kampanye::STATUS as $s)
        <a href="{{ route('kampanye.kelola', ['status' => $s]) }}" @class(['aktif' => $status === $s]) @if ($status === $s) aria-current="page" @endif>
            {{ $s }} <span class="fl-saring-jumlah">{{ $jumlah[$s] ?? 0 }}</span>
        </a>
    @endforeach
</nav>

<x-panel judul="Daftar kampanye">
    @if ($kampanye->isEmpty())
        <x-kosong judul="Belum ada kampanye" ikon="bendera">
            @if ($status)
                Tidak ada kampanye berstatus {{ $status }}.
            @else
                Pilih Buat kampanye untuk membuat kampanye pertama.
            @endif
        </x-kosong>
    @else
        <div class="fl-tabel">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama kampanye</th>
                        <th>Lokasi</th>
                        <th>Tanggal pelaksanaan</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($kampanye as $k)
                        <tr>
                            <td>{{ $k->nama_kampanye }}</td>
                            <td>{{ $k->lokasi }}</td>
                            <td class="fl-data">
                                {{ \App\Support\Waktu::tanggal($k->tanggal_mulai) }}
                                @if (! $k->tanggal_mulai->isSameDay($k->tanggal_selesai))
                                    - {{ \App\Support\Waktu::tanggal($k->tanggal_selesai) }}
                                @endif
                            </td>
                            <td><x-status :nilai="$k->status_kampanye" /></td>
                            <td class="text-end">
                                @if ($k->bisaDiubah())
                                    <a href="{{ route('kampanye.edit', $k) }}" class="btn btn-sekunder btn-sm">Ubah</a>
                                    <button type="button" class="btn btn-outline-danger btn-sm"
                                        data-bs-toggle="modal" data-bs-target="#modalBatal"
                                        data-nama="{{ $k->nama_kampanye }}"
                                        data-aksi="{{ route('kampanye.batalkan', $k) }}">Batalkan</button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-panel>

<div class="modal fade" id="modalBatal" tabindex="-1" aria-labelledby="judulModalBatal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" id="formBatal" class="modal-content">
            @csrf
            @method('PATCH')
            <div class="modal-header">
                <h2 class="modal-title fs-5" id="judulModalBatal">Batalkan kampanye?</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Kampanye <strong id="namaBatal"></strong> tidak akan tampil lagi kepada Pengguna dan tidak dapat diubah setelah dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sekunder" data-bs-dismiss="modal">Kembali</button>
                <button type="submit" class="btn btn-bahaya">Batalkan kampanye</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('modalBatal').addEventListener('show.bs.modal', function (e) {
        var tombol = e.relatedTarget;
        document.getElementById('formBatal').action = tombol.dataset.aksi;
        document.getElementById('namaBatal').textContent = tombol.dataset.nama;
    });
</script>
@endpush