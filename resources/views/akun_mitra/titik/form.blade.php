@extends('layouts.app')

@php
    $ubah = $titik->exists;
    $judul = $ubah ? 'Ubah titik pengambilan' : 'Tambah titik pengambilan';
@endphp

@section('title', $judul)

@section('content')
    <x-kepala-halaman :judul="$judul" :kembali="route('titik-pengambilan.index')" teks-kembali="Kembali ke daftar titik" />

    <x-panel class="fl-kartu-isi">
        <form method="POST" novalidate
            action="{{ $ubah ? route('titik-pengambilan.update', $titik) : route('titik-pengambilan.store') }}">
            @csrf
            @if ($ubah)
                @method('PUT')
            @endif

            <div class="mb-3">
                <label for="nama_titik" class="form-label">Nama titik</label>
                <input type="text" id="nama_titik" name="nama_titik" value="{{ old('nama_titik', $titik->nama_titik) }}"
                    class="form-control @error('nama_titik') is-invalid @enderror" placeholder="Mis. Dapur utama"
                    maxlength="100" required @if (! $ubah) autofocus @endif
                    @error('nama_titik') aria-describedby="galatNama" aria-invalid="true" @enderror>
                @error('nama_titik')
                    <div class="invalid-feedback" id="galatNama">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="alamat" class="form-label">Alamat</label>
                <input type="text" id="alamat" name="alamat" value="{{ old('alamat', $titik->alamat) }}"
                    class="form-control @error('alamat') is-invalid @enderror" placeholder="Jalan, nomor, kelurahan"
                    maxlength="255" autocomplete="street-address" required
                    @error('alamat') aria-describedby="galatAlamat" aria-invalid="true" @enderror>
                @error('alamat')
                    <div class="invalid-feedback" id="galatAlamat">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="patokan" class="form-label">Patokan <span class="fl-opsional">opsional</span></label>
                <input type="text" id="patokan" name="patokan" value="{{ old('patokan', $titik->patokan) }}"
                    class="form-control @error('patokan') is-invalid @enderror" placeholder="Mis. Pintu belakang, dekat parkiran"
                    maxlength="255" aria-describedby="bantuanPatokan @error('patokan') galatPatokan @enderror">
                <div class="form-text" id="bantuanPatokan">Membantu penerima menemukan tempat pengambilan.</div>
                @error('patokan')
                    <div class="invalid-feedback" id="galatPatokan">{{ $message }}</div>
                @enderror
            </div>

            <fieldset class="fl-koordinat">
                <div class="fl-koordinat-kepala">
                    <legend class="form-label mb-0">Koordinat</legend>
                    <button type="button" class="btn btn-sekunder btn-sm" id="tombolLokasi"
                        aria-describedby="statusLokasi"><x-ikon nama="lokasi" />Gunakan lokasi saya</button>
                </div>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label for="latitude" class="form-label fl-label-kecil">Latitude</label>
                        <input type="text" id="latitude" name="latitude" value="{{ old('latitude', $titik->latitude) }}"
                            class="form-control fl-data @error('latitude') is-invalid @enderror" placeholder="Mis. -6.8915"
                            inputmode="decimal" required
                            @error('latitude') aria-describedby="galatLatitude" aria-invalid="true" @enderror>
                        @error('latitude')
                            <div class="invalid-feedback" id="galatLatitude">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-sm-6">
                        <label for="longitude" class="form-label fl-label-kecil">Longitude</label>
                        <input type="text" id="longitude" name="longitude" value="{{ old('longitude', $titik->longitude) }}"
                            class="form-control fl-data @error('longitude') is-invalid @enderror" placeholder="Mis. 107.6107"
                            inputmode="decimal" required
                            @error('longitude') aria-describedby="galatLongitude" aria-invalid="true" @enderror>
                        @error('longitude')
                            <div class="invalid-feedback" id="galatLongitude">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="form-text" id="statusLokasi" role="status" aria-live="polite">
                    Isi manual, atau tekan "Gunakan lokasi saya" saat kamu sedang berada di titik ini.
                </div>
            </fieldset>

            <div class="fl-form-aksi">
                <a href="{{ route('titik-pengambilan.index') }}" class="btn btn-sekunder">Batal</a>
                <button type="submit" class="btn btn-utama" data-memproses="Menyimpan">{{ $ubah ? 'Simpan perubahan' : 'Simpan titik' }}</button>
            </div>
        </form>
    </x-panel>
@endsection

@push('scripts')
    <script>
        // Isi latitude dan longitude dari lokasi perangkat (geolocation bawaan browser).
        (function () {
            const tombol = document.getElementById('tombolLokasi');
            const status = document.getElementById('statusLokasi');

            if (!('geolocation' in navigator)) {
                tombol.hidden = true;
                status.textContent = 'Browser ini tidak mendukung lokasi otomatis. Isi koordinat secara manual.';
                return;
            }

            tombol.addEventListener('click', function () {
                tombol.disabled = true;
                status.textContent = 'Mencari lokasimu...';

                navigator.geolocation.getCurrentPosition(function (posisi) {
                    document.getElementById('latitude').value = posisi.coords.latitude.toFixed(7);
                    document.getElementById('longitude').value = posisi.coords.longitude.toFixed(7);
                    status.textContent = 'Koordinat terisi dari lokasimu (akurasi sekitar ' + Math.round(posisi.coords.accuracy) + ' m). Periksa lagi sebelum menyimpan.';
                    tombol.disabled = false;
                }, function (galat) {
                    status.textContent = galat.code === 1
                        ? 'Izin lokasi ditolak. Isi koordinat secara manual.'
                        : 'Lokasi tidak bisa didapat. Isi koordinat secara manual.';
                    tombol.disabled = false;
                }, { enableHighAccuracy: true, timeout: 10000 });
            });
        })();
    </script>
@endpush
