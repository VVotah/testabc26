@extends('layouts.app')

@section('title', 'Konfirmasi Serah Terima')

@section('content')
    <x-kepala-halaman judul="Konfirmasi Serah Terima"
        :kembali="route('pickup.index')" teks-kembali="Kembali ke jadwal pickup"
        keterangan="Masukkan kode unik pickup dari penerima atau relawan untuk memvalidasi penyerahan donasi." />

    @include('partials.pesan')

    <div class="row g-4">
        <div class="col-12 col-xxl-5">
            <x-panel judul="Validasi kode pickup">
                <form action="{{ route('pickup.konfirmasi.store') }}" method="POST" class="mt-3">
                    @csrf

                    <div class="mb-3">
                        <label for="kode_pickup" class="form-label">Kode pickup</label>
                        <input type="text" id="kode_pickup" name="kode_pickup"
                            class="form-control fl-data @error('kode_pickup') is-invalid @enderror"
                            value="{{ old('kode_pickup') }}"
                            placeholder="Mis. FL-1001" required autofocus>
                        @error('kode_pickup')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Minta penerima atau relawan menunjukkan kode pickup saat serah terima di lokasi.
                        </div>
                    </div>

                    <div class="fl-form-aksi">
                        <button type="submit" class="btn btn-utama">
                            Validasi kode
                        </button>
                    </div>
                </form>
            </x-panel>
        </div>

        <div class="col-12 col-xxl-7">
            <x-panel judul="Daftar serah terima Anda">
                @if ($daftarKonfirmasi->isEmpty())
                    <x-kosong judul="Belum ada data serah terima" ikon="kotak">
                        Jadwal pickup yang menunggu konfirmasi serah terima akan tampil di sini.
                    </x-kosong>
                @else
                    <div class="fl-tabel mt-3">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Jadwal</th>
                                    <th>Slot waktu</th>
                                    <th>Titik pengambilan</th>
                                    <th>Penerima</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($daftarKonfirmasi as $item)
                                    <tr>
                                        <td class="fl-data">#{{ $item->id_jadwal_pickup }}</td>
                                        <td class="fl-data">{{ $item->jadwalPickup?->slot_waktu?->translatedFormat('d M Y, H.i') }} WIB</td>
                                        <td>{{ $item->jadwalPickup?->titikPengambilan?->nama_titik ?? '-' }}</td>
                                        <td>{{ $item->jadwalPickup?->penerima?->nama ?? '-' }}</td>
                                        <td><x-status :nilai="$item->status_serah_terima" /></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-panel>
        </div>
    </div>
@endsection
