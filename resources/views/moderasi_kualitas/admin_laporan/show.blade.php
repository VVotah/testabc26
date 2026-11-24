@extends('layouts.app')

@section('title', 'Tinjau Laporan #'.$laporan->id_laporan_masalah)

@php
    use App\Models\LaporanMasalah;
    use App\Support\Waktu;

    $bisaDitangguhkan = $alasanTidakBisaDitangguhkan === null;
    $namaJenis = strtolower($laporan->jenis_objek);
    $statusDipilih = old('status_laporan', in_array($laporan->status_laporan, LaporanMasalah::STATUS_TINDAK_LANJUT, true) ? $laporan->status_laporan : '');
    $aksiDipilih = old('aksi_penangguhan', 'tidak_ada');
@endphp

@section('content')
    <x-kepala-halaman judul="Tinjau laporan masalah" :kembali="route('laporan-masalah.index')" teks-kembali="Kembali ke laporan masuk">
        <x-status :nilai="$laporan->status_laporan" />
    </x-kepala-halaman>

    @include('partials.pesan')

    <div class="row g-4">
        <div class="col-12 col-xl-7 d-grid gap-4 align-content-start">
            <x-panel judul="Data laporan">
                <dl class="fl-rincian">
                    <dt>Objek dilaporkan</dt>
                    <dd>
                        {{ $laporan->nama_objek }}
                        <span class="fl-cap ms-1">{{ $laporan->jenis_objek }}</span>
                        @if ($laporan->status_objek)
                            <div class="mt-1"><x-status :nilai="$laporan->status_objek" /></div>
                        @endif
                    </dd>
                    <dt>Kategori masalah</dt>
                    <dd>{{ $laporan->kategori_masalah }}</dd>
                    <dt>Deskripsi</dt>
                    <dd>
                        @if ($laporan->deskripsi_masalah)
                            {{ $laporan->deskripsi_masalah }}
                        @else
                            <span class="text-muted">Tidak ada deskripsi.</span>
                        @endif
                    </dd>
                    <dt>Pelapor</dt>
                    <dd>{{ $laporan->pelapor?->nama ?? 'Pengguna tidak ditemukan' }}</dd>
                    <dt>Dilaporkan pada</dt>
                    <dd class="fl-data">{{ $laporan->waktu_laporan ? Waktu::lengkap($laporan->waktu_laporan) : '-' }}</dd>
                </dl>
            </x-panel>

            <x-panel judul="Riwayat tindak lanjut">
                <ol class="fl-linimasa">
                    <li>
                        <span class="fl-linimasa-judul">Laporan dikirim oleh {{ $laporan->pelapor?->nama ?? 'pengguna' }}</span>
                        <span class="fl-linimasa-waktu">{{ $laporan->waktu_laporan ? Waktu::lengkap($laporan->waktu_laporan) : '-' }}</span>
                    </li>
                    @forelse ($laporan->tindakLanjut as $tindak)
                        <li @class(['negatif' => $tindak->objek_ditangguhkan])>
                            <span class="fl-linimasa-judul">{{ $tindak->jenis_tindakan }} oleh {{ $tindak->admin?->nama ?? 'Admin' }}</span>
                            <span class="fl-linimasa-waktu">{{ $tindak->waktu_tindak_lanjut ? Waktu::lengkap($tindak->waktu_tindak_lanjut) : '-' }}</span>
                            @if ($tindak->objek_ditangguhkan)
                                <span class="fl-linimasa-ket">{{ $tindak->objek_ditangguhkan }} ditangguhkan.</span>
                            @endif
                            @if ($tindak->balasan_pelapor)
                                <span class="fl-linimasa-ket">Balasan: {{ $tindak->balasan_pelapor }}</span>
                            @endif
                        </li>
                    @empty
                        <li class="tunggu">
                            <span class="fl-linimasa-judul">Menunggu tinjauan admin</span>
                            <span class="fl-linimasa-waktu">-</span>
                        </li>
                    @endforelse
                </ol>
            </x-panel>
        </div>

        <div class="col-12 col-xl-5 d-grid gap-4 align-content-start">
            <x-panel judul="Bukti foto">
                @if ($laporan->bukti_foto)
                    @php $urlFoto = asset('storage/'.$laporan->bukti_foto); @endphp
                    <img src="{{ $urlFoto }}" alt="Bukti laporan {{ $laporan->nama_objek }}" class="fl-pratinjau">
                    <div class="fl-pratinjau-berkas">
                        <span class="fl-data small">{{ basename($laporan->bukti_foto) }}</span>
                        <a href="{{ $urlFoto }}" target="_blank" rel="noopener" class="fw-bold">Buka</a>
                    </div>
                @else
                    <x-kosong judul="Tidak ada bukti foto" ikon="unggah">
                        Admin tetap dapat memeriksa informasi laporan lainnya.
                    </x-kosong>
                @endif
            </x-panel>

            <x-panel judul="Tindak lanjut">
                <form method="POST" action="{{ route('laporan-masalah.tindak-lanjuti', $laporan->id_laporan_masalah) }}"
                    id="formTindakLanjut" novalidate>
                    @csrf
                    <input type="hidden" name="konfirmasi_penangguhan" id="konfirmasiPenangguhan" value="0">

                    <div class="mb-3">
                        <label for="status_laporan" class="form-label">Status laporan</label>
                        <select name="status_laporan" id="status_laporan" required
                            class="form-select @error('status_laporan') is-invalid @enderror">
                            <option value="">Pilih status</option>
                            @foreach (LaporanMasalah::STATUS_TINDAK_LANJUT as $status)
                                <option value="{{ $status }}" @selected($statusDipilih === $status)>{{ $status }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">Pilih Ditolak bila laporan tidak terbukti.</div>
                        @error('status_laporan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="jenis_tindakan" class="form-label">Jenis tindakan</label>
                        <input type="text" name="jenis_tindakan" id="jenis_tindakan" maxlength="255" required
                            class="form-control @error('jenis_tindakan') is-invalid @enderror"
                            value="{{ old('jenis_tindakan') }}" placeholder="Mis. Pemeriksaan laporan">
                        @error('jenis_tindakan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <fieldset class="mb-3">
                        <legend class="form-label">Penangguhan</legend>
                        @if ($bisaDitangguhkan)
                            <div class="fl-pilihan fl-pilihan-tumpuk @error('aksi_penangguhan') is-invalid @enderror @error('konfirmasi_penangguhan') is-invalid @enderror">
                                <label class="fl-pilihan-kartu">
                                    <input type="radio" name="aksi_penangguhan" value="tidak_ada" @checked($aksiDipilih !== 'tangguhkan')>
                                    <span class="fl-pilihan-ikon"><x-ikon nama="centang" /></span>
                                    <span class="fl-pilihan-judul">Tidak menangguhkan</span>
                                    <span class="fl-pilihan-ket">{{ $laporan->jenis_objek }} tetap aktif.</span>
                                </label>
                                <label class="fl-pilihan-kartu fl-pilihan-bahaya">
                                    <input type="radio" name="aksi_penangguhan" value="tangguhkan" @checked($aksiDipilih === 'tangguhkan')>
                                    <span class="fl-pilihan-ikon"><x-ikon nama="perisai" /></span>
                                    <span class="fl-pilihan-judul">Tangguhkan {{ $namaJenis }}</span>
                                    <span class="fl-pilihan-ket">
                                        @if ($laporan->jenis_objek === LaporanMasalah::OBJEK_AKUN)
                                            Pemilik akun langsung dikeluarkan dan tidak bisa masuk.
                                        @else
                                            Listing tidak tampil lagi di katalog.
                                        @endif
                                    </span>
                                </label>
                            </div>
                            @error('aksi_penangguhan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @error('konfirmasi_penangguhan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        @else
                            <input type="hidden" name="aksi_penangguhan" value="tidak_ada">
                            <p class="form-text mt-0 mb-0">{{ $alasanTidakBisaDitangguhkan }}</p>
                        @endif
                    </fieldset>

                    <div class="mb-3">
                        <label for="balasan_pelapor" class="form-label">Balasan kepada pelapor <span class="fl-opsional">opsional</span></label>
                        <textarea name="balasan_pelapor" id="balasan_pelapor" rows="3"
                            class="form-control @error('balasan_pelapor') is-invalid @enderror"
                            placeholder="Tuliskan hasil pemeriksaan atau tanggapan kepada pelapor.">{{ old('balasan_pelapor') }}</textarea>
                        @error('balasan_pelapor')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="fl-form-aksi">
                        <button type="submit" class="btn btn-utama" data-memproses="Menyimpan">Simpan tindak lanjut</button>
                    </div>
                </form>
            </x-panel>
        </div>
    </div>

    @if ($bisaDitangguhkan)
        @include('moderasi_kualitas.admin_laporan._modal-tangguhkan')
    @endif
@endsection
