@extends('layouts.app')

@section('title', 'Detail Listing')

@section('content')
    <x-kepala-halaman
        judul="Detail Listing"
        keterangan="Informasi lengkap makanan yang kamu bagikan."
        :kembali="route('listing.index')"
        teks-kembali="Kembali ke daftar listing"
    />

    <x-panel class="fl-kartu-isi">
        <div class="row g-4">
            <div class="col-md-5">
                @if ($listing->foto_url)
                    <img
                        src="{{ $listing->foto_url }}"
                        alt="{{ $listing->nama_makanan }}"
                        class="img-fluid rounded w-100"
                    >
                @else
                    <x-kosong judul="Belum ada foto" ikon="kotak" />
                @endif
            </div>

            <div class="col-md-7">
                <div class="mb-3">
                    <span class="fl-cap">
                        {{ $listing->kategori?->nama_kategori }}
                    </span>

                    <h4 class="mt-2 mb-2">
                        {{ $listing->nama_makanan }}
                    </h4>

                    <x-status :nilai="$listing->status_listing" />
                </div>

                <dl class="fl-rincian">
                    <div>
                        <dt>Tipe listing</dt>
                        <dd>{{ $listing->tipe_harga_teks }}</dd>
                    </div>

                    <div>
                        <dt>Jumlah porsi</dt>
                        <dd>{{ $listing->jumlah_porsi }} porsi</dd>
                    </div>

                    <div>
                        <dt>Batas kedaluwarsa</dt>
                        <dd>
                            {{ \App\Support\Waktu::lengkap($listing->batas_kedaluwarsa) }}
                        </dd>
                    </div>

                    <div>
                        <dt>Titik pengambilan</dt>
                        <dd>
                            {{ $listing->titikPengambilan?->nama_titik ?? '-' }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="fl-form-aksi mt-4">
            <a href="{{ route('listing.index') }}" class="btn btn-sekunder">
                Kembali
            </a>

            @if ($listing->status_listing === \App\Models\Listing::STATUS_AKTIF)
                <a
                    href="{{ route('listing.edit', $listing) }}"
                    class="btn btn-sekunder"
                >
                    Ubah
                </a>

                <form
                    action="{{ route('listing.close', $listing) }}"
                    method="POST"
                >
                    @csrf

                    <button type="submit" class="btn btn-sekunder">
                        Tutup Listing
                    </button>
                </form>
            @endif

            <button
                type="button"
                class="btn btn-outline-danger"
                data-bs-toggle="modal"
                data-bs-target="#modalHapusListing"
                data-nama="{{ $listing->nama_makanan }}"
                data-aksi="{{ route('listing.destroy', $listing) }}"
            >
                Hapus Listing
            </button>
        </div>
    </x-panel>

    {{-- Modal konfirmasi hapus listing --}}
    <div
        class="modal fade"
        id="modalHapusListing"
        tabindex="-1"
        aria-labelledby="judulHapusListing"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body p-4">
                    <h2 class="modal-title" id="judulHapusListing">
                        Hapus listing ini?
                    </h2>

                    <p class="text-muted mt-2 mb-4">
                        Listing
                        <strong class="text-body" id="namaListingHapus">
                            ini
                        </strong>
                        akan dihapus secara permanen.
                    </p>

                    <form
                        method="POST"
                        action=""
                        id="formHapusListing"
                        class="fl-form-aksi mt-0"
                    >
                        @csrf
                        @method('DELETE')

                        <button
                            type="button"
                            class="btn btn-sekunder"
                            data-bs-dismiss="modal"
                        >
                            Batal
                        </button>

                        <button
                            type="submit"
                            class="btn btn-bahaya"
                            data-memproses="Menghapus"
                        >
                            Ya, hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('modalHapusListing').addEventListener('show.bs.modal', function (e) {
            const pemicu = e.relatedTarget;

            document.getElementById('formHapusListing').action = pemicu.dataset.aksi;

            document.getElementById('namaListingHapus').textContent =
                '"' + pemicu.dataset.nama + '"';
        });
    </script>
@endpush
