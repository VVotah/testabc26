
@extends('layouts.app')

@php
    $ubah = $jadwal->exists;
    $judul = $ubah ? 'Ubah jadwal surplus' : 'Tambah jadwal surplus';

    $daftarHari = [
        'Senin',
        'Selasa',
        'Rabu',
        'Kamis',
        'Jumat',
        'Sabtu',
        'Minggu',
    ];

    $hariDipilih = old('hari', $jadwal->hari);

    $nilaiJam = old(
        'jam',
        $ubah && $jadwal->jam
            ? \Carbon\Carbon::parse($jadwal->jam)->format('H:i')
            : ''
    );
@endphp

@section('title', $judul)

@section('content')
    <x-kepala-halaman
        :judul="$judul"
        :kembali="route('listing.jadwal-surplus.index')"
        teks-kembali="Kembali ke jadwal surplus"
    />

    <x-panel class="fl-kartu-isi">
        <form
            method="POST"
            action="{{ $ubah
                ? route('listing.jadwal-surplus.update', $jadwal)
                : route('listing.jadwal-surplus.store') }}"
        >
            @csrf

            @if ($ubah)
                @method('PUT')
            @endif

            {{-- Hari surplus --}}
            <div class="mb-3">
                <label for="hari" class="form-label">
                    Hari surplus
                </label>

                <select
                    id="hari"
                    name="hari"
                    class="form-select @error('hari') is-invalid @enderror"
                    required
                >
                    <option value="">Pilih hari</option>

                    @foreach ($daftarHari as $hari)
                        <option
                            value="{{ $hari }}"
                            @selected($hariDipilih === $hari)
                        >
                            {{ $hari }}
                        </option>
                    @endforeach
                </select>

                @error('hari')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Jam surplus --}}
            <div class="mb-3">
                <label for="jam" class="form-label">
                    Jam surplus
                </label>

                <input
                    type="time"
                    id="jam"
                    name="jam"
                    value="{{ $nilaiJam }}"
                    class="form-control @error('jam') is-invalid @enderror"
                    required
                >

                <div class="form-text">
                    Tentukan jam rutin ketersediaan surplus makanan.
                </div>

                @error('jam')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Estimasi porsi --}}
            <div class="mb-3">
                <label for="estimasi_porsi" class="form-label">
                    Estimasi porsi
                </label>

                <input
                    type="number"
                    id="estimasi_porsi"
                    name="estimasi_porsi"
                    value="{{ old('estimasi_porsi', $jadwal->estimasi_porsi) }}"
                    class="form-control @error('estimasi_porsi') is-invalid @enderror"
                    min="1"
                    placeholder="Mis. 20"
                    required
                >

                <div class="form-text">
                    Perkiraan jumlah porsi makanan surplus yang tersedia.
                </div>

                @error('estimasi_porsi')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Tombol aksi --}}
            <div class="fl-form-aksi">
                <a
                    href="{{ route('listing.jadwal-surplus.index') }}"
                    class="btn btn-sekunder"
                >
                    Batal
                </a>

                <button type="submit" class="btn btn-utama">
                    {{ $ubah ? 'Simpan perubahan' : 'Simpan jadwal' }}
                </button>
            </div>
        </form>
    </x-panel>
@endsection
