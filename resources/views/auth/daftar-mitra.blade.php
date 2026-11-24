@extends('layouts.tamu')

@section('title', 'Daftar sebagai mitra')

@section('content')
    <header class="fl-tamu-kepala">
        <a href="{{ route('login') }}"><img src="{{ asset('img/logo.png') }}" alt="FoodLoop" width="148" height="49"></a>
        <span class="small">Sudah punya akun? <a href="{{ route('login') }}" class="fw-bold">Masuk</a></span>
    </header>

    <main class="fl-tamu-isi">
        <div class="fl-tamu-judul">
            <h1>Daftar sebagai mitra</h1>
            <p>Isi data usahamu. Admin akan memverifikasi dalam 1-3 hari kerja.</p>
        </div>

        <div class="fl-panel fl-kartu-form">
            @include('partials.pesan')

            <form method="POST" action="{{ route('register.mitra.simpan') }}" enctype="multipart/form-data" novalidate>
                @csrf

                <h2 class="fl-form-bagian">Data usaha</h2>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="nama_mitra" class="form-label">Nama usaha</label>
                        <input type="text" id="nama_mitra" name="nama_mitra" value="{{ old('nama_mitra') }}"
                            class="form-control @error('nama_mitra') is-invalid @enderror" placeholder="Mis. Warung Bu Sari"
                            autocomplete="organization" required
                            @error('nama_mitra') aria-describedby="galatNamaMitra" aria-invalid="true" @enderror>
                        @error('nama_mitra')
                            <div class="invalid-feedback" id="galatNamaMitra">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="jenis_mitra" class="form-label">Jenis mitra</label>
                        <select id="jenis_mitra" name="jenis_mitra" class="form-select @error('jenis_mitra') is-invalid @enderror" required
                            @error('jenis_mitra') aria-describedby="galatJenis" aria-invalid="true" @enderror>
                            <option value="" disabled @selected(! old('jenis_mitra'))>Pilih jenis mitra</option>
                            @foreach ($jenisMitra as $jenis)
                                <option value="{{ $jenis }}" @selected(old('jenis_mitra') === $jenis)>{{ $jenis }}</option>
                            @endforeach
                        </select>
                        @error('jenis_mitra')
                            <div class="invalid-feedback" id="galatJenis">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <label for="nama" class="form-label">Nama penanggung jawab</label>
                        <input type="text" id="nama" name="nama" value="{{ old('nama') }}"
                            class="form-control @error('nama') is-invalid @enderror" placeholder="Nama lengkap"
                            autocomplete="name" required
                            @error('nama') aria-describedby="galatNama" aria-invalid="true" @enderror>
                        @error('nama')
                            <div class="invalid-feedback" id="galatNama">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <label for="alamat_jalan" class="form-label">Alamat lengkap</label>
                        <textarea id="alamat_jalan" name="alamat_jalan" rows="2"
                            class="form-control @error('alamat_jalan') is-invalid @enderror" placeholder="Jalan, nomor, kelurahan"
                            autocomplete="street-address" required
                            @error('alamat_jalan') aria-describedby="galatAlamat" aria-invalid="true" @enderror>{{ old('alamat_jalan') }}</textarea>
                        @error('alamat_jalan')
                            <div class="invalid-feedback" id="galatAlamat">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="kota" class="form-label">Kota</label>
                        <input type="text" id="kota" name="kota" value="{{ old('kota') }}"
                            class="form-control @error('kota') is-invalid @enderror" placeholder="Mis. Bandung"
                            autocomplete="address-level2" required
                            @error('kota') aria-describedby="galatKota" aria-invalid="true" @enderror>
                        @error('kota')
                            <div class="invalid-feedback" id="galatKota">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="kode_pos" class="form-label">Kode pos</label>
                        <input type="text" id="kode_pos" name="kode_pos" value="{{ old('kode_pos') }}"
                            class="form-control @error('kode_pos') is-invalid @enderror" placeholder="Mis. 40257"
                            autocomplete="postal-code" inputmode="numeric" maxlength="5" pattern="[0-9]{5}" required
                            @error('kode_pos') aria-describedby="galatKodePos" aria-invalid="true" @enderror>
                        @error('kode_pos')
                            <div class="invalid-feedback" id="galatKodePos">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <h2 class="fl-form-bagian">Akun</h2>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                            class="form-control @error('email') is-invalid @enderror" placeholder="nama@email.com"
                            autocomplete="email" inputmode="email" required
                            @error('email') aria-describedby="galatEmail" aria-invalid="true" @enderror>
                        @error('email')
                            <div class="invalid-feedback" id="galatEmail">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="kata_sandi" class="form-label">Kata sandi</label>
                        <div class="fl-gabung @error('kata_sandi') is-invalid @enderror">
                            <input type="password" id="kata_sandi" name="kata_sandi" class="form-control"
                                placeholder="Minimal 8 karakter" autocomplete="new-password" minlength="8" required
                                @error('kata_sandi') aria-describedby="galatSandi" aria-invalid="true" @enderror>
                            <button type="button" class="fl-lihat-sandi" aria-controls="kata_sandi" aria-pressed="false">Lihat</button>
                        </div>
                        @error('kata_sandi')
                            <div class="invalid-feedback" id="galatSandi">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <h2 class="fl-form-bagian">Dokumen verifikasi</h2>
                <label class="fl-unggah @error('bukti_pendukung') is-invalid @enderror" for="bukti_pendukung">
                    <input type="file" id="bukti_pendukung" name="bukti_pendukung" accept=".pdf,.jpg,.jpeg,.png" required
                        @error('bukti_pendukung') aria-describedby="galatBukti" aria-invalid="true" @enderror>
                    <span class="fl-unggah-ikon"><x-ikon nama="dokumen" /></span>
                    <span class="fl-unggah-judul">Seret berkas ke sini atau <u>pilih berkas</u></span>
                    <span class="fl-unggah-ket">NIB atau KTP penanggung jawab. PDF, JPG, atau PNG, maks. 5 MB.</span>
                </label>
                @error('bukti_pendukung')
                    <div class="invalid-feedback" id="galatBukti">{{ $message }}</div>
                @enderror
                <div class="form-text mt-2">Dokumen hanya dapat dilihat Admin FoodLoop untuk verifikasi.</div>

                <div class="form-check mt-4">
                    <input type="checkbox" class="form-check-input @error('setuju') is-invalid @enderror" id="setuju" name="setuju" value="1"
                        @checked(old('setuju')) required>
                    <label class="form-check-label" for="setuju">Saya menyatakan data benar dan menyetujui syarat dan ketentuan FoodLoop.</label>
                    @error('setuju')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="fl-form-aksi">
                    <a href="{{ route('login') }}" class="btn btn-sekunder">Batal</a>
                    <button type="submit" class="btn btn-utama" data-memproses="Mengirim pengajuan">Ajukan verifikasi</button>
                </div>
            </form>
        </div>
    </main>
@endsection
