@extends('layouts.app')

@section('content')
    <x-kepala-halaman
        judul="Detail Postingan"
        :kembali="route('forum.index')"
    />

    @include('partials.pesan')

    <div class="card">
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    @if ($postingan->foto_url)
                        <img src="{{ $postingan->foto_url }}"
                             alt="{{ $postingan->nama_makanan }}"
                             class="img-fluid w-100">
                    @else
                        <div class="ratio ratio-16x9 bg-light d-flex align-items-center justify-content-center text-muted">
                            Foto makanan
                        </div>
                    @endif
                </div>

                <div class="col-md-6">
                    <h2 class="h4 fw-bold">
                        {{ $postingan->nama_makanan }}
                    </h2>

                    <div class="mb-3">
                        <x-status :nilai="$postingan->status_postingan" />
                    </div>

                    <dl class="fl-rincian">
                        <dt>Sisa Porsi</dt>
                        <dd>{{ $postingan->sisa_porsi }} porsi</dd>

                        <dt>Lokasi</dt>
                        <dd>{{ $postingan->lokasi_pengambilan }}</dd>

                        <dt>Batas Waktu</dt>
                        <dd>
                            {{ \App\Support\Waktu::lengkap($postingan->batas_waktu) }}
                        </dd>

                        <dt>Diunggah oleh</dt>
                        <dd>
                            @if ($adalahPemilik)
                                Saya ({{ $postingan->pengunggah->nama }})
                            @else
                                {{ $postingan->pengunggah->nama }}
                            @endif
                        </dd>
                    </dl>

                    @if ($bisaKelola)
                        <div class="d-flex flex-wrap gap-2 mt-3 mb-2">
                            <a href="{{ route('forum.edit', $postingan) }}"
                               class="btn btn-outline-secondary">
                                Ubah Postingan
                            </a>

                            <button type="button"
                                    class="btn btn-outline-secondary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalTutup">
                                Tutup Postingan
                            </button>

                            <button type="button"
                                    class="btn btn-outline-danger"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalHapus">
                                Hapus Postingan
                            </button>
                        </div>

                        {{-- TODO PBI-15: sambungkan ke halaman Klaim Masuk (beserta jumlah klaim) --}}
                        <a class="btn btn-sekunder disabled" aria-disabled="true">
                            Lihat Klaim Masuk
                        </a>
                    @endif

                    @if (! $adalahPemilik)
                        <button type="button"
                                class="btn btn-outline-danger mt-3"
                                data-bs-toggle="modal"
                                data-bs-target="#modalLaporkan">
                            Laporkan Postingan
                        </button>
                    @endif
                </div>
            </div>

            <hr class="my-4">

            {{-- PBI-14: Klaim Porsi --}}
            <h3 class="h5 fw-bold mb-3">Klaim Porsi</h3>

            @if ($bisaKlaim)
                <form method="POST" action="{{ route('forum.klaim.store', $postingan) }}" class="mb-4">
                    @csrf

                    <label for="jumlah_porsi" class="form-label">Jumlah Porsi yang Ingin Diklaim</label>

                    <div class="row g-2">
                        <div class="col-md-4">
                            <input type="number"
                                   name="jumlah_porsi"
                                   id="jumlah_porsi"
                                   class="form-control @error('jumlah_porsi') is-invalid @enderror"
                                   min="1"
                                   max="{{ $postingan->sisa_porsi }}"
                                   value="{{ old('jumlah_porsi') }}"
                                   required>

                            @error('jumlah_porsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-auto">
                            <button type="submit" class="btn btn-utama">Ajukan Klaim</button>
                        </div>
                    </div>

                    <small class="text-muted">Maksimal {{ $postingan->sisa_porsi }} porsi.</small>
                </form>
            @elseif (! $adalahPemilik)
                <p class="text-muted">
                    Postingan ini tidak dapat diklaim karena sudah tidak tersedia
                    atau batas waktu pengambilan telah berakhir.
                </p>
            @endif

            @if ($klaimSaya->isNotEmpty())
                <h4 class="h6 fw-bold">Riwayat Klaim Saya</h4>

                @foreach ($klaimSaya as $klaim)
                    <div class="border rounded p-3 mb-2">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <div class="fw-semibold">{{ $klaim->jumlah_porsi }} porsi</div>
                                <small class="text-muted">{{ $klaim->waktu_pengajuan->format('d M Y, H:i') }}</small>
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                <x-status :nilai="$klaim->status_klaim" />

                                @if ($klaim->status_klaim === \App\Models\KlaimPorsi::STATUS_MENUNGGU)
                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalBatalKlaim"
                                            data-nama="{{ $klaim->jumlah_porsi }} porsi"
                                            data-aksi="{{ route('forum.klaim.destroy', [$postingan, $klaim]) }}">
                                        Batalkan
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif

            <hr class="my-4">

            {{-- PBI-14: Komentar --}}
            <h3 class="h5 fw-bold mb-3">Komentar</h3>

            @if ($bolehKomentar)
                <form method="POST" action="{{ route('forum.komentar.store', $postingan) }}" class="mb-4">
                    @csrf

                    <label for="isi_komentar" class="form-label">Tulis Komentar</label>

                    <textarea name="isi_komentar"
                              id="isi_komentar"
                              rows="3"
                              maxlength="1000"
                              class="form-control @error('isi_komentar') is-invalid @enderror"
                              placeholder="Tulis komentar kamu..."
                              required>{{ old('isi_komentar') }}</textarea>

                    @error('isi_komentar')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Maksimal 1000 karakter.</div>

                    <button type="submit" class="btn btn-sekunder mt-2">Kirim Komentar</button>
                </form>
            @else
                <p class="text-muted">
                    Kamu harus menjadi pemilik postingan atau punya klaim yang tidak dibatalkan
                    untuk menulis komentar.
                </p>
            @endif

            @if ($komentar->isNotEmpty())
                @foreach ($komentar as $item)
                    <div class="border rounded p-3 mb-2">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <div>
                                <div class="fw-semibold">{{ $item->penulis->nama }}</div>
                                <small class="text-muted">{{ $item->waktu_komentar->format('d M Y, H:i') }}</small>
                            </div>

                            @if ((int) $item->id_penulis === (int) auth()->id())
                                <button type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalHapusKomentar"
                                        data-aksi="{{ route('forum.komentar.destroy', [$postingan, $item]) }}">
                                    Hapus
                                </button>
                            @endif
                        </div>

                        <p class="mb-0 mt-2">{{ $item->isi_komentar }}</p>
                    </div>
                @endforeach
            @else
                <p class="text-muted mb-0">Belum ada komentar pada postingan ini.</p>
            @endif
        </div>
    </div>

    @include('forum_sharing._modal-klaim-komentar')
    {{-- PBI-19: Modal Laporkan Postingan --}}
    @if (! $adalahPemilik)
        <div class="modal fade" id="modalLaporkan" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST"
                        action="{{ route('laporan-masalah.store') }}"
                        enctype="multipart/form-data">
                        @csrf

                        <input type="hidden"
                            name="jenis_objek"
                            value="{{ \App\Models\LaporanMasalah::OBJEK_POSTINGAN_SHARING }}">

                        <input type="hidden"
                            name="id_objek"
                            value="{{ $postingan->id_postingan_sharing }}">

                        <div class="modal-body p-4">
                            <h4 class="fw-bold">Laporkan Postingan</h4>

                            <p class="text-muted">
                                Bantu kami menjaga Forum Sharing dengan melaporkan
                                postingan yang bermasalah.
                            </p>

                            <div class="mb-3">
                                <label for="kategori_masalah" class="form-label">
                                    Kategori Masalah
                                </label>

                                <select name="kategori_masalah"
                                        id="kategori_masalah"
                                        class="form-select"
                                        required>
                                    <option value="">Pilih kategori</option>
                                    <option value="Kedaluwarsa">Kedaluwarsa</option>
                                    <option value="Tidak sesuai">Tidak sesuai</option>
                                    <option value="Spam">Spam</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="deskripsi_masalah" class="form-label">
                                    Deskripsi Masalah
                                </label>

                                <textarea name="deskripsi_masalah"
                                        id="deskripsi_masalah"
                                        class="form-control"
                                        rows="3"
                                        placeholder="Jelaskan masalah postingan ini..."></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="bukti_foto" class="form-label">
                                    Bukti Foto
                                </label>

                                <input type="file"
                                    name="bukti_foto"
                                    id="bukti_foto"
                                    class="form-control"
                                    accept=".jpg,.jpeg,.png"
                                    required>

                                <small class="text-muted">
                                    Format JPG, JPEG, atau PNG. Maksimal 5 MB.
                                </small>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <button type="button"
                                        class="btn btn-outline-secondary"
                                        data-bs-dismiss="modal">
                                    Batal
                                </button>

                                <button type="submit" class="btn btn-danger">
                                    Kirim Laporan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @if ($bisaKelola)
        {{-- SCR-13.5 Konfirmasi Tutup Postingan --}}
        <div class="modal fade" id="modalTutup" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body p-4">
                        <h4 class="fw-bold">Tutup postingan ini?</h4>

                        <p class="text-muted">
                            Status postingan akan berubah menjadi Ditutup
                            dan tidak dapat diklaim lagi.
                        </p>

                        <form method="POST"
                              action="{{ route('forum.tutup', $postingan) }}"
                              class="d-flex justify-content-end gap-2">
                            @csrf
                            @method('PATCH')

                            <button type="button"
                                    class="btn btn-outline-secondary"
                                    data-bs-dismiss="modal">
                                Batal
                            </button>

                            <button type="submit" class="btn btn-dark">
                                Ya, tutup
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- SCR-13.6 Konfirmasi Hapus Postingan --}}
        <div class="modal fade" id="modalHapus" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body p-4">
                        <h4 class="fw-bold">Hapus postingan ini?</h4>

                        <p class="text-muted">
                            Postingan beserta klaim dan komentarnya akan
                            dihapus dan tidak dapat dikembalikan.
                            Postingan yang sudah memiliki klaim Disetujui
                            tidak dapat dihapus.
                        </p>

                        <form method="POST"
                              action="{{ route('forum.destroy', $postingan) }}"
                              class="d-flex justify-content-end gap-2">
                            @csrf
                            @method('DELETE')

                            <button type="button"
                                    class="btn btn-outline-secondary"
                                    data-bs-dismiss="modal">
                                Batal
                            </button>

                            <button type="submit" class="btn btn-bahaya">
                                Ya, hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
