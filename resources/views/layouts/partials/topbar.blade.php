@php
    $hariSingkat = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'][now()->dayOfWeek];
    $bulanSingkat = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'][now()->month - 1];
@endphp

<header class="fl-topbar">
    <button type="button" class="btn btn-sekunder btn-sm d-lg-none" id="tombolMenu"
        aria-controls="sidebar" aria-expanded="false">Menu</button>

    {{-- Cap tanggal dan jam: waktu adalah inti FoodLoop, jadi selalu terlihat. --}}
    <div class="fl-waktu" aria-label="Waktu sekarang">
        <span class="fl-tanggal">{{ $hariSingkat }} {{ now()->format('d') }} {{ $bulanSingkat }} {{ now()->format('Y') }}</span>
        <b id="jamSekarang">{{ now()->format('H.i') }}</b>
    </div>

    <div class="ms-auto d-flex align-items-center gap-2">
        @if (Route::has('notifikasi.index'))
            <a href="{{ route('notifikasi.index') }}" class="btn btn-sekunder btn-sm">Notifikasi</a>
        @endif

        <div class="dropdown">
            <button class="fl-tombol-akun" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="fl-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr(auth()->user()->nama, 0, 1)) }}</span>
                <span class="fl-nama">{{ auth()->user()->nama }}</span>
            </button>
            <div class="dropdown-menu dropdown-menu-end p-0" style="min-width: 248px;">
                <div class="px-3 py-3">
                    <div class="fw-bold">{{ auth()->user()->nama }}</div>
                    <div class="fl-cap mt-1">{{ auth()->user()->peran?->nama_peran }}</div>
                </div>
                <div class="dropdown-divider m-0"></div>
                @if (Route::has('profil.edit'))
                    <a class="dropdown-item px-3" href="{{ route('profil.edit') }}">Profil saya</a>
                @else
                    <span class="dropdown-item px-3 disabled d-flex justify-content-between align-items-center" aria-disabled="true">
                        Profil saya <small class="fl-segera">Segera</small>
                    </span>
                @endif
                <div class="dropdown-divider m-0"></div>
                <button type="button" class="dropdown-item px-3" data-bs-toggle="modal" data-bs-target="#modalKeluar">Keluar</button>
            </div>
        </div>
    </div>
</header>
