@php($artikel = $artikel ?? null)
@php($sudahTerbit = $artikel && $artikel->status_artikel === \App\Models\Artikel::TERBIT)

<x-panel class="fl-kartu-isi">
    <form action="{{ $aksi }}" method="POST" enctype="multipart/form-data" novalidate>
        @csrf
        @if ($artikel)
            @method('PUT')
        @endif

        <h2 class="fl-form-bagian">Isi artikel</h2>

        <div class="mb-3">
            <label for="judul" class="form-label">Judul</label>
            <input type="text" id="judul" name="judul" maxlength="150"
                class="form-control @error('judul') is-invalid @enderror"
                value="{{ old('judul', $artikel->judul ?? '') }}">
            @error('judul') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="isi" class="form-label">
                Isi artikel
                @unless ($sudahTerbit)
                    <span class="fl-opsional">wajib saat diterbitkan</span>
                @endunless
            </label>
            <textarea id="isi" name="isi" rows="12"
                class="form-control @error('isi') is-invalid @enderror">{{ old('isi', $artikel->isi ?? '') }}</textarea>
            @error('isi') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <h2 class="fl-form-bagian">Gambar pendukung</h2>

        @if ($artikel && $artikel->url_gambar)
            <div class="mb-3">
                <img src="{{ $artikel->url_gambar }}" alt="Gambar artikel" class="fl-pratinjau">
                <div class="form-text">Pilih berkas baru di bawah untuk mengganti gambar.</div>
            </div>
        @endif

        <div class="mb-3">
            <label class="fl-unggah">
                <input type="file" name="gambar" accept="image/png,image/jpeg">
                <span class="fl-unggah-ikon"><x-ikon nama="unggah" /></span>
                <span class="fl-unggah-judul">Pilih gambar</span>
                <span class="fl-unggah-ket">JPG atau PNG, maksimal 5 MB</span>
            </label>
            @error('gambar') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        <div class="fl-form-aksi">
            <a href="{{ route('artikel.kelola') }}" class="btn btn-sekunder">Batal</a>
            @if (! $artikel)
                {{-- C: artikel baru selalu disimpan sebagai Draf --}}
                <button type="submit" class="btn btn-utama" data-memproses="Menyimpan">Simpan sebagai Draf</button>
            @elseif ($artikel->status_artikel === \App\Models\Artikel::DRAF)
                {{-- U: ubah isi, atau Draf menjadi Terbit --}}
                <button type="submit" name="aksi" value="simpan" class="btn btn-sekunder">Simpan perubahan</button>
                <button type="submit" name="aksi" value="terbit" class="btn btn-utama">Terbitkan</button>
            @else
                <button type="submit" name="aksi" value="simpan" class="btn btn-utama" data-memproses="Menyimpan">Simpan perubahan</button>
            @endif
        </div>
    </form>
</x-panel>