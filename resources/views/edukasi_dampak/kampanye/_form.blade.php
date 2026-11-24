@php($kampanye = $kampanye ?? null)

<x-panel class="fl-kartu-isi">
    <form action="{{ $aksi }}" method="POST" novalidate>
        @csrf
        @if ($kampanye)
            @method('PUT')
        @endif

        <h2 class="fl-form-bagian">Informasi kampanye</h2>

        <div class="mb-3">
            <label for="nama_kampanye" class="form-label">Nama kampanye</label>
            <input type="text" id="nama_kampanye" name="nama_kampanye" maxlength="150"
                class="form-control @error('nama_kampanye') is-invalid @enderror"
                value="{{ old('nama_kampanye', $kampanye->nama_kampanye ?? '') }}">
            @error('nama_kampanye') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="deskripsi" class="form-label">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi" rows="5"
                class="form-control @error('deskripsi') is-invalid @enderror">{{ old('deskripsi', $kampanye->deskripsi ?? '') }}</textarea>
            @error('deskripsi') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="lokasi" class="form-label">Lokasi</label>
            <input type="text" id="lokasi" name="lokasi" maxlength="255"
                class="form-control @error('lokasi') is-invalid @enderror"
                value="{{ old('lokasi', $kampanye->lokasi ?? '') }}">
            @error('lokasi') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <h2 class="fl-form-bagian">Tanggal pelaksanaan</h2>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label for="tanggal_mulai" class="form-label">Tanggal mulai</label>
                <input type="date" id="tanggal_mulai" name="tanggal_mulai"
                    class="form-control fl-data @error('tanggal_mulai') is-invalid @enderror"
                    value="{{ old('tanggal_mulai', optional($kampanye?->tanggal_mulai)->format('Y-m-d')) }}">
                @error('tanggal_mulai') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label for="tanggal_selesai" class="form-label">Tanggal selesai</label>
                <input type="date" id="tanggal_selesai" name="tanggal_selesai"
                    class="form-control fl-data @error('tanggal_selesai') is-invalid @enderror"
                    value="{{ old('tanggal_selesai', optional($kampanye?->tanggal_selesai)->format('Y-m-d')) }}">
                @error('tanggal_selesai') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="fl-form-aksi">
            <a href="{{ route('kampanye.kelola') }}" class="btn btn-sekunder">Batal</a>
            <button type="submit" class="btn btn-utama" data-memproses="Menyimpan">Simpan</button>
        </div>
    </form>
</x-panel>