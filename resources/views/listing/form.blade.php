@extends('layouts.app')

@php
    $ubah = $listing->exists;
    $judul = $ubah ? 'Ubah listing' : 'Tambah listing';
    $tipeDipilih = old('tipe_listing', $listing->tipe_listing);

    $ikonTipe = [
        'Gratis' => 'hati',
        'Diskon' => 'kotak',
    ];

    $keteranganTipe = [
        'Gratis' => 'Penerima mengambil tanpa biaya.',
        'Diskon' => 'Penerima membayar harga yang kamu tentukan.',
    ];

    $nilaiKedaluwarsa = old(
        'batas_kedaluwarsa',
        $ubah && $listing->batas_kedaluwarsa
            ? $listing->batas_kedaluwarsa->format('Y-m-d\TH:i')
            : ''
    );
@endphp

@section('title', $judul)

@section('content')
    <x-kepala-halaman
        :judul="$judul"
        :kembali="route('listing.index')"
        teks-kembali="Kembali ke daftar listing"
    />

    <x-panel class="fl-kartu-isi">
        <form method="POST"
            action="{{ $ubah ? route('listing.update', $listing) : route('listing.store') }}"
            enctype="multipart/form-data">

            @csrf

            @if ($ubah)
                @method('PUT')
            @endif

            <div class="mb-3">
                <label for="foto" class="form-label">Foto makanan</label>

                @if ($ubah && $listing->foto_url)
                    <div class="mb-2">
                        <img
                            src="{{ $listing->foto_url }}"
                            alt="{{ $listing->nama_makanan }}"
                            class="fl-pratinjau"
                        >
                    </div>
                    <div class="form-text mb-2">
                        Foto saat ini. Unggah foto baru jika ingin menggantinya.
                    </div>
                @endif

                <input type="file"
                    id="foto"
                    name="foto"
                    accept="image/jpeg,image/png"
                    class="form-control @error('foto') is-invalid @enderror"
                    @required(! $ubah)>

                <div class="form-text">JPG atau PNG, maksimal 5 MB.</div>

                @error('foto')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="nama_makanan" class="form-label">Nama makanan</label>
                <input type="text"
                    id="nama_makanan"
                    name="nama_makanan"
                    value="{{ old('nama_makanan', $listing->nama_makanan) }}"
                    class="form-control @error('nama_makanan') is-invalid @enderror"
                    placeholder="Mis. Nasi ayam teriyaki"
                    maxlength="100"
                    required>

                @error('nama_makanan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="jumlah_porsi" class="form-label">Jumlah porsi</label>
                <input type="number"
                    id="jumlah_porsi"
                    name="jumlah_porsi"
                    value="{{ old('jumlah_porsi', $listing->jumlah_porsi) }}"
                    class="form-control @error('jumlah_porsi') is-invalid @enderror"
                    min="{{ $ubah ? 0 : 1 }}"
                    max="100000"
                    placeholder="Mis. 10"
                    required>

                @error('jumlah_porsi')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <span class="form-label d-block">Tipe listing</span>

                <div class="fl-pilihan" role="radiogroup" aria-label="Tipe listing">
                    @foreach (\App\Models\Listing::TIPE as $tipe)
                        <label class="fl-pilihan-kartu">
                            <input type="radio"
                                name="tipe_listing"
                                value="{{ $tipe }}"
                                @checked($tipeDipilih === $tipe)
                                required>

                            <span class="fl-pilihan-ikon">
                                <x-ikon :nama="$ikonTipe[$tipe]" />
                            </span>

                            <span class="fl-pilihan-judul">{{ $tipe }}</span>
                            <span class="fl-pilihan-ket">
                                {{ $keteranganTipe[$tipe] }}
                            </span>
                        </label>
                    @endforeach
                </div>

                @error('tipe_listing')
                    <div class="text-danger small mt-2">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3" id="bagianHarga" hidden>
                <label for="harga" class="form-label">Harga (Rp)</label>
                <input type="number"
                    id="harga"
                    name="harga"
                    value="{{ old('harga', $listing->harga) }}"
                    class="form-control @error('harga') is-invalid @enderror"
                    min="1"
                    placeholder="Mis. 10000"
                    disabled>

                @error('harga')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="batas_kedaluwarsa" class="form-label">
                    Batas kedaluwarsa
                </label>

                <input type="datetime-local"
                    id="batas_kedaluwarsa"
                    name="batas_kedaluwarsa"
                    value="{{ $nilaiKedaluwarsa }}"
                    min="{{ now()->format('Y-m-d\TH:i') }}"
                    class="form-control @error('batas_kedaluwarsa') is-invalid @enderror"
                    required>

                @error('batas_kedaluwarsa')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="id_kategori_makanan" class="form-label">
                    Kategori makanan
                </label>

                <select id="id_kategori_makanan"
                    name="id_kategori_makanan"
                    class="form-select @error('id_kategori_makanan') is-invalid @enderror"
                    required>

                    <option value="">Pilih kategori</option>

                    @foreach ($daftarKategori as $kategori)
                        <option value="{{ $kategori->id_kategori_makanan }}"
                            @selected((string) old('id_kategori_makanan', $listing->id_kategori_makanan) === (string) $kategori->id_kategori_makanan)>
                            {{ $kategori->nama_kategori }}
                        </option>
                    @endforeach
                </select>

                @error('id_kategori_makanan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="id_titik" class="form-label">
                    Titik pengambilan
                </label>

                <select id="id_titik"
                    name="id_titik"
                    class="form-select @error('id_titik') is-invalid @enderror"
                    required>

                    <option value="">Pilih titik pengambilan</option>

                    @foreach ($daftarTitik as $titik)
                        <option value="{{ $titik->id_titik }}"
                            @selected((string) old('id_titik', $listing->id_titik) === (string) $titik->id_titik)>
                            {{ $titik->nama_titik }}
                        </option>
                    @endforeach
                </select>

                @error('id_titik')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="fl-form-aksi">
                <a href="{{ route('listing.index') }}" class="btn btn-sekunder">
                    Batal
                </a>

                <button type="submit" class="btn btn-utama">
                    {{ $ubah ? 'Simpan perubahan' : 'Simpan listing' }}
                </button>
            </div>
        </form>
    </x-panel>
@endsection

@push('scripts')
    <script>
        (function () {
            const bagian = document.getElementById('bagianHarga');
            const harga = document.getElementById('harga');
            const radios = document.querySelectorAll(
                'input[name="tipe_listing"]'
            );

            function aturHarga() {
                const diskon = document.querySelector(
                    'input[name="tipe_listing"]:checked'
                )?.value === 'Diskon';

                bagian.hidden = !diskon;
                harga.disabled = !diskon;
                harga.required = diskon;

                if (!diskon) {
                    harga.value = '';
                }
            }

            radios.forEach(function (radio) {
                radio.addEventListener('change', aturHarga);
            });

            aturHarga();
        })();
    </script>
@endpush
