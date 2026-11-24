@extends('layouts.app')

@section('title', 'Tinjau '.$mitra->nama_mitra)

@php use App\Support\Waktu; @endphp

@section('content')
    <x-kepala-halaman judul="Tinjau pengajuan mitra" :kembali="route('verifikasi-mitra.index')" teks-kembali="Kembali ke antrean verifikasi">
        <x-status :nilai="$pengajuan->status" />
    </x-kepala-halaman>

    @include('partials.pesan')

    <div class="row g-4">
        <div class="col-12 col-xl-7 d-grid gap-4 align-content-start">
            <x-panel judul="Data pengajuan">
                <dl class="fl-rincian">
                    <dt>Nama usaha</dt>
                    <dd>{{ $mitra->nama_mitra }}</dd>
                    <dt>Jenis mitra</dt>
                    <dd>{{ $mitra->jenis_mitra }}</dd>
                    <dt>Penanggung jawab</dt>
                    <dd>{{ $mitra->pengguna->nama }}</dd>
                    <dt>Email</dt>
                    <dd>{{ $mitra->pengguna->email }}</dd>
                    <dt>Alamat</dt>
                    <dd>{{ $mitra->alamat_jalan }}<br>{{ $mitra->kota }} <span class="fl-data">{{ $mitra->kode_pos }}</span></dd>
                    <dt>Diajukan pada</dt>
                    <dd class="fl-data">{{ Waktu::lengkap($pengajuan->tanggal_pengajuan) }}</dd>
                </dl>
            </x-panel>

            <x-panel judul="Riwayat pengajuan">
                <ol class="fl-linimasa">
                    @foreach ($riwayat as $item)
                        <li>
                            <span class="fl-linimasa-judul">Pengajuan dikirim</span>
                            <span class="fl-linimasa-waktu">{{ Waktu::lengkap($item->tanggal_pengajuan) }}</span>
                        </li>
                        @if ($item->sudahDiputuskan())
                            <li @class([$item->keputusan === \App\Models\VerifikasiMitra::KEPUTUSAN_SETUJU ? 'positif' : 'negatif'])>
                                <span class="fl-linimasa-judul">{{ $item->keputusan === \App\Models\VerifikasiMitra::KEPUTUSAN_SETUJU ? 'Disetujui' : 'Ditolak' }} oleh {{ $item->admin?->nama ?? 'Admin' }}</span>
                                <span class="fl-linimasa-waktu">{{ Waktu::lengkap($item->tanggal_keputusan) }}</span>
                                @if ($item->alasan)
                                    <span class="fl-linimasa-ket">Alasan: {{ $item->alasan }}</span>
                                @endif
                            </li>
                        @else
                            <li class="tunggu">
                                <span class="fl-linimasa-judul">Menunggu tinjauan admin</span>
                                <span class="fl-linimasa-waktu">-</span>
                            </li>
                        @endif
                    @endforeach
                </ol>
            </x-panel>
        </div>

        <div class="col-12 col-xl-5 d-grid gap-4 align-content-start">
            <x-panel judul="Dokumen verifikasi">
                @php $urlDokumen = route('verifikasi-mitra.dokumen', $pengajuan); @endphp
                @if ($dokumenGambar)
                    <img src="{{ $urlDokumen }}" alt="Dokumen verifikasi {{ $mitra->nama_mitra }}" class="fl-pratinjau">
                @else
                    <iframe src="{{ $urlDokumen }}" title="Dokumen verifikasi {{ $mitra->nama_mitra }}" class="fl-pratinjau"></iframe>
                @endif
                <div class="fl-pratinjau-berkas">
                    <span class="fl-data small">{{ $namaDokumen }}</span>
                    <a href="{{ $urlDokumen }}" target="_blank" rel="noopener" class="fw-bold">Buka</a>
                </div>
            </x-panel>

            @include('akun_mitra.verifikasi._keputusan')
        </div>
    </div>
@endsection
