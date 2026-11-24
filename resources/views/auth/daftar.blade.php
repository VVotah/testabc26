@extends('layouts.tamu')

@section('title', 'Daftar')

@section('content')
    <header class="fl-tamu-kepala">
        <a href="{{ route('login') }}"><img src="{{ asset('img/logo.png') }}" alt="FoodLoop" width="148" height="49"></a>
        <span class="small">Sudah punya akun? <a href="{{ route('login') }}" class="fw-bold">Masuk</a></span>
    </header>

    <main class="fl-tamu-isi">
        <div class="fl-tamu-judul">
            <h1>Buat akun</h1>
            <p>Untuk mencari dan meminta makanan, atau membantu mengantarnya.</p>
        </div>

        <div class="fl-panel fl-kartu-form">
            <form method="POST" action="{{ route('register.simpan') }}" id="formDaftar" novalidate>
                @csrf

                <fieldset class="mb-4">
                    <legend class="form-label">Daftar sebagai</legend>
                    <div class="fl-pilihan @error('peran') is-invalid @enderror" role="radiogroup">
                        <label class="fl-pilihan-kartu">
                            <input type="radio" name="peran" value="penerima" @checked(old('peran', 'penerima') === 'penerima')>
                            <span class="fl-pilihan-ikon"><x-ikon nama="hati" /></span>
                            <span class="fl-pilihan-judul">Penerima</span>
                            <span class="fl-pilihan-ket">Mencari makanan berlebih dan mengajukan permintaan.</span>
                        </label>
                        <label class="fl-pilihan-kartu">
                            <input type="radio" name="peran" value="relawan" @checked(old('peran') === 'relawan')>
                            <span class="fl-pilihan-ikon"><x-ikon nama="sepeda" /></span>
                            <span class="fl-pilihan-judul">Relawan</span>
                            <span class="fl-pilihan-ket">Mengambil tugas antar makanan ke penerima.</span>
                        </label>
                    </div>
                    @error('peran')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @if (Route::has('register.mitra'))
                        <div class="form-text mt-2">Punya usaha atau acara dengan makanan berlebih?
                            <a href="{{ route('register.mitra') }}" class="fw-bold">Daftar sebagai mitra</a>.</div>
                    @endif
                </fieldset>

                <div class="mb-3">
                    <label for="nama" class="form-label">Nama lengkap</label>
                    <input type="text" id="nama" name="nama" value="{{ old('nama') }}"
                        class="form-control @error('nama') is-invalid @enderror" autocomplete="name" required
                        @error('nama') aria-describedby="galatNama" aria-invalid="true" @enderror>
                    @error('nama')
                        <div class="invalid-feedback" id="galatNama">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                        class="form-control @error('email') is-invalid @enderror" placeholder="nama@email.com"
                        autocomplete="email" inputmode="email" required
                        @error('email') aria-describedby="galatEmail" aria-invalid="true" @enderror>
                    @error('email')
                        <div class="invalid-feedback" id="galatEmail">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
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

                <div class="form-check mt-4">
                    <input type="checkbox" class="form-check-input @error('setuju') is-invalid @enderror" id="setuju" name="setuju" value="1"
                        @checked(old('setuju')) required>
                    <label class="form-check-label" for="setuju">Saya menyatakan data ini benar dan menyetujui syarat dan ketentuan FoodLoop.</label>
                    @error('setuju')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="fl-form-aksi">
                    <a href="{{ route('login') }}" class="btn btn-sekunder">Batal</a>
                    <button type="submit" class="btn btn-utama" data-memproses="Membuat akun">Buat akun</button>
                </div>
            </form>
        </div>
    </main>
@endsection
