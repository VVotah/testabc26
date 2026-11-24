@extends('layouts.tamu')

@section('title', 'Masuk')

@php
    $hariSingkat = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'][now()->dayOfWeek];
    $bulanSingkat = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'][now()->month - 1];
@endphp

@section('content')
    <div class="fl-masuk">
        <section class="fl-masuk-rak" aria-label="Tentang FoodLoop">
            <img src="{{ asset('img/logo.png') }}" alt="FoodLoop" class="fl-masuk-logo" width="168" height="56">

            <div class="fl-masuk-pesan">
                <div class="fl-masuk-teks">
                    <h1><span>Makanan</span> <span>berlebih,</span> <span>sampai ke yang</span> <span>membutuhkan.</span></h1>
                    <p>Setiap makanan di FoodLoop punya tenggat, sisa porsi, dan kode serah terima yang tercatat.</p>
                </div>

                {{-- Label contoh: gambaran sebuah listing. Datanya contoh, bukan dari database. --}}
                <figure class="fl-label-contoh fl-cetak">
                    <div class="fl-label-kertas">
                        <div class="fl-label-pita"><span>FoodLoop</span><span>Listing</span></div>
                        <div class="fl-label-isi">
                            <div class="fl-label-nama">Roti &amp; pastry</div>
                            <div class="fl-label-mitra">Toko roti, titik Warung Utama</div>
                            <div class="fl-label-waktu">
                                <span class="fl-cap">Layak sampai</span>
                                <strong>19.00</strong>
                                <span class="fl-cap">{{ $hariSingkat }} {{ now()->format('d') }} {{ $bulanSingkat }} {{ now()->format('Y') }}</span>
                            </div>
                        </div>
                        <div class="fl-label-rinci">
                            <div><span class="fl-cap">Sisa porsi</span><b>24</b></div>
                            <div><span class="fl-cap">Ambil di</span><b>Coblong</b></div>
                        </div>
                        <div class="fl-label-kode">
                            <span class="fl-pindai" aria-hidden="true"></span>
                            <span class="fl-data">FL-7K2Q</span>
                        </div>
                    </div>
                    <span class="fl-stiker">Gratis</span>
                    <figcaption>Contoh label listing</figcaption>
                </figure>
            </div>

            <div class="fl-masuk-kaki">Mendukung SDG 12: konsumsi dan produksi yang bertanggung jawab</div>
        </section>

        <main class="fl-masuk-form">
            <div class="fl-masuk-kotak">
                <h2>Masuk</h2>
                <p>Pakai akun FoodLoop kamu.</p>

                @include('partials.pesan')

                <form method="POST" action="{{ route('login.proses') }}" id="formMasuk" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                            class="form-control @error('email') is-invalid @enderror"
                            autocomplete="email" inputmode="email" autofocus required
                            @error('email') aria-describedby="galatEmail" aria-invalid="true" @enderror>
                        @error('email')
                            <div class="invalid-feedback" id="galatEmail">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="kata_sandi" class="form-label">Kata sandi</label>
                        <div class="fl-gabung @error('kata_sandi') is-invalid @enderror">
                            <input type="password" id="kata_sandi" name="kata_sandi" class="form-control"
                                autocomplete="current-password" required
                                @error('kata_sandi') aria-describedby="galatSandi" aria-invalid="true" @enderror>
                            <button type="button" id="lihatSandi" aria-controls="kata_sandi" aria-pressed="false">Lihat</button>
                        </div>
                        @error('kata_sandi')
                            <div class="invalid-feedback" id="galatSandi">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-check mb-4">
                        <input type="checkbox" class="form-check-input" id="ingat_saya" name="ingat_saya" value="1"
                            @checked(old('ingat_saya'))>
                        <label class="form-check-label" for="ingat_saya">Ingat saya di perangkat ini</label>
                    </div>

                    <button type="submit" class="btn btn-utama btn-lg w-100" id="tombolMasuk">Masuk</button>
                </form>

                @if (Route::has('register') || Route::has('register.mitra'))
                    <div class="fl-masuk-lain">
                        @if (Route::has('register'))
                            <div>Belum punya akun? <a href="{{ route('register') }}">Daftar</a></div>
                        @endif
                        @if (Route::has('register.mitra'))
                            <div>Punya usaha atau acara? <a href="{{ route('register.mitra') }}">Daftar sebagai mitra</a></div>
                        @endif
                    </div>
                @endif
            </div>
        </main>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('lihatSandi').addEventListener('click', function () {
            const kolom = document.getElementById('kata_sandi');
            const tampil = kolom.type === 'password';
            kolom.type = tampil ? 'text' : 'password';
            this.textContent = tampil ? 'Tutup' : 'Lihat';
            this.setAttribute('aria-pressed', tampil);
        });

        // Cegah kirim ganda dan beri tahu bahwa sedang diproses.
        document.getElementById('formMasuk').addEventListener('submit', function () {
            const tombol = document.getElementById('tombolMasuk');
            tombol.classList.add('memproses');
            tombol.textContent = 'Memproses';
        });
    </script>
@endpush
