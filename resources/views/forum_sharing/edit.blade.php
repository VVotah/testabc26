
@extends('layouts.app')

@section('content')
    <x-kepala-halaman
        judul="Ubah Postingan Sharing"
        :kembali="route('forum.index')"
    />

    <x-panel class="fl-kartu-isi">
        <form method="POST"
              action="{{ route('forum.update', $postingan) }}"
              enctype="multipart/form-data"
              novalidate>
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">
                    Ganti Foto Makanan
                    <span class="fl-opsional">opsional</span>
                </label>

                <div class="form-text mb-2">
                    Foto saat ini: {{ basename($postingan->foto_makanan) }}
                </div>

                <label class="fl-unggah">
                    <input type="file"
                            id="foto_makanan"
                           name="foto_makanan"
                           accept=".jpg,.jpeg,.png"
                           class="@error('foto_makanan') is-invalid @enderror">

                    <span class="fl-unggah-ikon">
                        <x-ikon nama="unggah" />
                    </span>

                    <span class="fl-unggah-judul">
                        Seret foto ke sini atau <u>pilih berkas</u>
                    </span>

                    <span class="fl-unggah-ket">
                        JPG atau PNG, maksimal 5 MB
                    </span>
                </label>
                <div id="preview-foto" class="d-none mt-3">
                    <div class="mb-2 fw-semibold">Preview Foto Baru</div>

                    <img id="preview-foto-gambar"
                        src=""
                        alt="Preview foto makanan"
                        class="img-fluid rounded">

                    <div id="preview-foto-nama"
                        class="form-text mt-2"
                        aria-live="polite">
                    </div>
                </div>
                @error('foto_makanan')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="nama_makanan" class="form-label">
                    Nama Makanan
                </label>

                <input type="text"
                       id="nama_makanan"
                       name="nama_makanan"
                       value="{{ old('nama_makanan', $postingan->nama_makanan) }}"
                       class="form-control @error('nama_makanan') is-invalid @enderror">

                @error('nama_makanan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="jumlah_porsi" class="form-label">
                    Jumlah Porsi
                </label>

                <input type="number"
                       id="jumlah_porsi"
                       name="jumlah_porsi"
                       min="1"
                       step="1"
                       value="{{ old('jumlah_porsi', $postingan->sisa_porsi) }}"
                       class="form-control @error('jumlah_porsi') is-invalid @enderror">

                @error('jumlah_porsi')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="lokasi_pengambilan" class="form-label">
                    Lokasi Pengambilan
                </label>

                <input type="text"
                       id="lokasi_pengambilan"
                       name="lokasi_pengambilan"
                       value="{{ old('lokasi_pengambilan', $postingan->lokasi_pengambilan) }}"
                       class="form-control @error('lokasi_pengambilan') is-invalid @enderror">

                @error('lokasi_pengambilan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="batas_waktu" class="form-label">
                    Batas Waktu Ketersediaan
                </label>

                <input type="datetime-local"
                       id="batas_waktu"
                       name="batas_waktu"
                       value="{{ old('batas_waktu', $postingan->batas_waktu->format('Y-m-d\TH:i')) }}"
                       class="form-control @error('batas_waktu') is-invalid @enderror">

                @error('batas_waktu')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="fl-form-aksi">
                <a href="{{ route('forum.show', $postingan) }}"
                   class="btn btn-sekunder">
                    Batal
                </a>

                <button type="submit" class="btn btn-utama">
                    Simpan
                </button>
            </div>
        </form>
    </x-panel>

<script>
    const inputFoto = document.getElementById('foto_makanan');
    const previewFoto = document.getElementById('preview-foto');
    const gambarPreview = document.getElementById('preview-foto-gambar');
    const namaPreview = document.getElementById('preview-foto-nama');

    let urlFotoSebelumnya = null;

    inputFoto.addEventListener('change', function () {
        const file = this.files[0];

        if (!file) {
            previewFoto.classList.add('d-none');
            return;
        }

        if (urlFotoSebelumnya) {
            URL.revokeObjectURL(urlFotoSebelumnya);
        }

        urlFotoSebelumnya = URL.createObjectURL(file);

        gambarPreview.src = urlFotoSebelumnya;
        namaPreview.textContent = file.name;

        previewFoto.classList.remove('d-none');
    });
</script>

@endsection
