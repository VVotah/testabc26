@extends('layouts.app')

@section('title', 'Jadwal Pickup')

@section('content')
    @php
        $bisaAjukan = auth()->user()->punyaPeran(\App\Models\Peran::PENERIMA);
        $donatur = auth()->user()->punyaPeran(\App\Models\Peran::MITRA);
    @endphp

    <x-kepala-halaman judul="Jadwal Pickup"
        :keterangan="$bisaAjukan
            ? 'Pilih waktu dan titik pengambilan donasi makanan yang telah disepakati.'
            : 'Jadwal pengambilan donasi makanan yang sudah diajukan.'">
        @if ($donatur)
            <a href="{{ route('pickup.konfirmasi') }}" class="btn btn-utama">
                <x-ikon nama="centang" />Konfirmasi serah terima
            </a>
        @endif
    </x-kepala-halaman>

    @include('partials.pesan')

    <div class="row g-4">
        @if ($bisaAjukan)
        <div class="col-12 col-xxl-5">
            <x-panel judul="Ajukan jadwal pickup">
                <form action="{{ route('pickup.store') }}" method="POST" class="mt-3">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="tanggal" class="form-label">Tanggal pengambilan</label>
                            <input type="date" id="tanggal" name="tanggal"
                                class="form-control @error('tanggal') is-invalid @enderror"
                                value="{{ old('tanggal', date('Y-m-d')) }}" required>
                            @error('tanggal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="jam" class="form-label">Slot waktu (WIB)</label>
                            <select id="jam" name="jam"
                                class="form-select @error('jam') is-invalid @enderror" required>
                                <option value="">Pilih slot waktu</option>
                                @foreach ([
                                    '09:00' => '09.00 - 10.00 WIB',
                                    '11:00' => '11.00 - 12.00 WIB',
                                    '13:00' => '13.00 - 14.00 WIB',
                                    '15:00' => '15.00 - 16.00 WIB',
                                    '17:00' => '17.00 - 18.00 WIB',
                                ] as $nilaiJam => $labelJam)
                                    <option value="{{ $nilaiJam }}" @selected(old('jam') === $nilaiJam)>
                                        {{ $labelJam }}
                                    </option>
                                @endforeach
                            </select>
                            @error('jam')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="id_titik" class="form-label">Titik pengambilan</label>
                            <select id="id_titik" name="id_titik"
                                class="form-select @error('id_titik') is-invalid @enderror" required>
                                <option value="">Pilih titik pengambilan</option>
                                @foreach ($titikList as $titik)
                                    <option value="{{ $titik->id_titik }}" @selected(old('id_titik') == $titik->id_titik)>
                                        {{ $titik->nama_titik }} — {{ $titik->alamat }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_titik')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if ($titikList->isEmpty())
                                <div class="form-text text-danger">Belum ada titik pengambilan yang tersedia.</div>
                            @endif
                        </div>
                    </div>

                    <div class="fl-form-aksi">
                        <button type="submit" class="btn btn-utama" @disabled($titikList->isEmpty())>
                            Ajukan jadwal
                        </button>
                    </div>
                </form>
            </x-panel>
        </div>
        @endif

        <div class="{{ $bisaAjukan ? 'col-12 col-xxl-7' : 'col-12' }}">
            <x-panel judul="Riwayat jadwal pickup">
                @if ($jadwals->isEmpty())
                    <x-kosong judul="Belum ada jadwal pickup" ikon="jam">
                        @if ($bisaAjukan)
                            Ajukan jadwal pengambilan melalui formulir untuk mulai menjadwalkan pickup.
                        @else
                            Jadwal pickup yang diajukan penerima akan tampil di sini.
                        @endif
                    </x-kosong>
                @else
                    <div class="fl-tabel mt-3">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    @if ($bisaAjukan)
                                        <th>Kode pickup</th>
                                    @endif
                                    <th>Slot waktu</th>
                                    <th>Titik pengambilan</th>
                                    <th>{{ $donatur ? 'Penerima' : 'Donatur' }}</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($jadwals as $jadwal)
                                    <tr>
                                        <td class="fl-data">#{{ $jadwal->id_jadwal_pickup }}</td>
                                        @if ($bisaAjukan)
                                            {{-- Kode pickup hanya untuk penerima pemilik jadwal (UC 4.1.12). --}}
                                            <td class="fl-data">
                                                {{ (int) $jadwal->id_penerima === (int) auth()->id() ? ($jadwal->konfirmasiSerahTerima?->kode_pickup ?? '-') : '-' }}
                                            </td>
                                        @endif
                                        <td class="fl-data">{{ $jadwal->slot_waktu?->translatedFormat('d M Y, H.i') }} WIB</td>
                                        <td>
                                            <div class="fw-semibold">{{ $jadwal->titikPengambilan->nama_titik ?? '-' }}</div>
                                            <small class="text-muted">{{ $jadwal->titikPengambilan->alamat ?? '' }}</small>
                                        </td>
                                        <td>{{ $donatur ? ($jadwal->penerima->nama ?? '-') : ($jadwal->donatur->nama ?? '-') }}</td>
                                        <td><x-status :nilai="$jadwal->status_jadwal" /></td>
                                        <td>
                                            @if ($donatur && $jadwal->status_jadwal === 'Menunggu')
                                                <div class="d-flex gap-1">
                                                    <form action="{{ route('pickup.setujui', $jadwal->id_jadwal_pickup) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-utama btn-sm">Setujui</button>
                                                    </form>
                                                    <form action="{{ route('pickup.batalkan', $jadwal->id_jadwal_pickup) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-outline-danger btn-sm">Tolak</button>
                                                    </form>
                                                </div>
                                            @elseif ($bisaAjukan && $jadwal->status_jadwal === 'Menunggu')
                                                <form action="{{ route('pickup.batalkan', $jadwal->id_jadwal_pickup) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-outline-secondary btn-sm">Batalkan</button>
                                                </form>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-panel>
        </div>
    </div>
@endsection
