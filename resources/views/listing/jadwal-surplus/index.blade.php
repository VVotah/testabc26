@extends('layouts.app')

@section('title', 'Jadwal Surplus Saya')

@section('content')
    <x-kepala-halaman
        judul="Jadwal Surplus Saya"
        keterangan="Atur jadwal surplus rutin untuk mempermudah penyaluran makanan berlebih."
    >
        <a href="{{ route('listing.jadwal-surplus.create') }}" class="btn btn-utama">
            <x-ikon nama="tambah" />
            Tambah jadwal
        </a>
    </x-kepala-halaman>

    @include('partials.pesan')

    <x-panel judul="Daftar jadwal surplus">
        @if ($daftarJadwal->isEmpty())
            <x-kosong judul="Belum ada jadwal surplus" ikon="kotak">
                Tambahkan jadwal surplus rutin agar kamu lebih mudah
                mengatur ketersediaan makanan setiap minggunya.

                <x-slot:aksi>
                    <a href="{{ route('listing.jadwal-surplus.create') }}" class="btn btn-utama">
                        <x-ikon nama="tambah" />
                        Tambah jadwal
                    </a>
                </x-slot:aksi>
            </x-kosong>
        @else
            <div class="fl-tabel">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Hari</th>
                            <th>Jam surplus</th>
                            <th>Estimasi porsi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($daftarJadwal as $jadwal)
                            <tr>
                                <td>{{ $jadwal->hari }}</td>
                                <td class="fl-data">
                                    {{ \Carbon\Carbon::parse($jadwal->jam)->format('H:i') }}
                                </td>
                                <td class="fl-data">
                                    {{ number_format($jadwal->estimasi_porsi, 0, ',', '.') }} porsi
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a
                                            href="{{ route('listing.jadwal-surplus.edit', $jadwal) }}"
                                            class="btn btn-sekunder btn-sm"
                                        >
                                            Edit
                                        </a>

                                        <button
                                            type="button"
                                            class="btn btn-sekunder btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modal-hapus-{{ $jadwal->id_jadwal_surplus }}"
                                        >
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Modal konfirmasi hapus --}}
            @foreach ($daftarJadwal as $jadwal)
                <div
                    class="modal fade"
                    id="modal-hapus-{{ $jadwal->id_jadwal_surplus }}"
                    tabindex="-1"
                    aria-labelledby="judul-modal-{{ $jadwal->id_jadwal_surplus }}"
                    aria-hidden="true"
                >
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5
                                    class="modal-title"
                                    id="judul-modal-{{ $jadwal->id_jadwal_surplus }}"
                                >
                                    Hapus jadwal surplus?
                                </h5>

                                <button
                                    type="button"
                                    class="btn-close"
                                    data-bs-dismiss="modal"
                                    aria-label="Tutup"
                                ></button>
                            </div>

                            <div class="modal-body">
                                Apakah kamu yakin ingin menghapus jadwal surplus
                                hari {{ $jadwal->hari }} pukul
                                {{ \Carbon\Carbon::parse($jadwal->jam)->format('H:i') }}?
                            </div>

                            <div class="modal-footer">
                                <button
                                    type="button"
                                    class="btn btn-sekunder"
                                    data-bs-dismiss="modal"
                                >
                                    Batal
                                </button>

                                <form
                                    action="{{ route('listing.jadwal-surplus.destroy', $jadwal) }}"
                                    method="POST"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-danger">
                                        Ya, hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </x-panel>
@endsection