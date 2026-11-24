{{--
    Judul halaman, dengan tombol aksi di kanan dan tautan kembali (opsional).
    Pakai: <x-kepala-halaman judul="Titik Pengambilan">
               <a href="..." class="btn btn-utama">+ Tambah titik</a>
           </x-kepala-halaman>
           <x-kepala-halaman judul="Tambah titik pengambilan" :kembali="route('...')" teks-kembali="Kembali ke daftar titik" />
           <x-kepala-halaman judul="..." keterangan="Satu kalimat penjelas." />
--}}
@props(['judul', 'keterangan' => null, 'kembali' => null, 'teksKembali' => 'Kembali'])

<div {{ $attributes->class('fl-kepala') }}>
    <div>
        @if ($kembali)
            <a href="{{ $kembali }}" class="fl-kembali">&larr; {{ $teksKembali }}</a>
        @endif
        <h1>{{ $judul }}</h1>
        @if ($keterangan)
            <p>{{ $keterangan }}</p>
        @endif
    </div>
    @if ($slot->isNotEmpty())
        <div class="fl-kepala-aksi">{{ $slot }}</div>
    @endif
</div>
