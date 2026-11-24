@extends('layouts.app')

@section('title', 'Profil Mitra')

@section('content')
    <x-kepala-halaman judul="Profil Mitra" keterangan="Data usaha yang dilihat Admin dan penerima. Mengubahnya tidak mengubah status verifikasi.">
        <x-status :nilai="$mitra->status_mitra" />
    </x-kepala-halaman>

    @include('partials.pesan')

    @if ($mitra->status_mitra === \App\Models\Mitra::STATUS_DITOLAK)
        @include('akun_mitra.profil._ajukan-ulang')
    @endif

    <x-panel class="fl-kartu-isi">
        <form method="POST" action="{{ route('profil-mitra.update') }}" novalidate>
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="nama_mitra" class="form-label">Nama usaha</label>
                    <input type="text" id="nama_mitra" name="nama_mitra" value="{{ old('nama_mitra', $mitra->nama_mitra) }}"
                        class="form-control @error('nama_mitra') is-invalid @enderror" placeholder="Mis. Warung Bu Sari"
                        maxlength="100" autocomplete="organization" required
                        @error('nama_mitra') aria-describedby="galatNamaMitra" aria-invalid="true" @enderror>
                    @error('nama_mitra')
                        <div class="invalid-feedback" id="galatNamaMitra">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="jenis_mitra" class="form-label">Jenis mitra</label>
                    <select id="jenis_mitra" name="jenis_mitra" class="form-select @error('jenis_mitra') is-invalid @enderror" required
                        @error('jenis_mitra') aria-describedby="galatJenis" aria-invalid="true" @enderror>
                        @foreach ($jenisMitra as $jenis)
                            <option value="{{ $jenis }}" @selected(old('jenis_mitra', $mitra->jenis_mitra) === $jenis)>{{ $jenis }}</option>
                        @endforeach
                    </select>
                    @error('jenis_mitra')
                        <div class="invalid-feedback" id="galatJenis">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12">
                    <label for="alamat_jalan" class="form-label">Alamat lengkap</label>
                    <input type="text" id="alamat_jalan" name="alamat_jalan" value="{{ old('alamat_jalan', $mitra->alamat_jalan) }}"
                        class="form-control @error('alamat_jalan') is-invalid @enderror" placeholder="Jalan, nomor, kelurahan"
                        maxlength="180" autocomplete="street-address" required
                        @error('alamat_jalan') aria-describedby="galatAlamat" aria-invalid="true" @enderror>
                    @error('alamat_jalan')
                        <div class="invalid-feedback" id="galatAlamat">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="kota" class="form-label">Kota</label>
                    <input type="text" id="kota" name="kota" value="{{ old('kota', $mitra->kota) }}"
                        class="form-control @error('kota') is-invalid @enderror" placeholder="Mis. Bandung"
                        maxlength="50" autocomplete="address-level2" required
                        @error('kota') aria-describedby="galatKota" aria-invalid="true" @enderror>
                    @error('kota')
                        <div class="invalid-feedback" id="galatKota">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="kode_pos" class="form-label">Kode pos</label>
                    <input type="text" id="kode_pos" name="kode_pos" value="{{ old('kode_pos', $mitra->kode_pos) }}"
                        class="form-control fl-data @error('kode_pos') is-invalid @enderror" placeholder="Mis. 40132"
                        inputmode="numeric" maxlength="5" autocomplete="postal-code" required
                        @error('kode_pos') aria-describedby="galatKodePos" aria-invalid="true" @enderror>
                    @error('kode_pos')
                        <div class="invalid-feedback" id="galatKodePos">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="fl-form-aksi">
                <a href="{{ route('dashboard') }}" class="btn btn-sekunder">Batal</a>
                <button type="submit" class="btn btn-utama" data-memproses="Menyimpan">Simpan perubahan</button>
            </div>
        </form>
    </x-panel>
@endsection
