{{--
    Tampilan saat daftar belum ada isinya. Beri tahu pengguna langkah berikutnya.
    Pakai: <x-kosong judul="Belum ada titik pengambilan" ikon="lokasi">
               Tambahkan titik supaya bisa dipilih saat membuat listing.
               <x-slot:aksi><a href="..." class="btn btn-utama btn-sm">+ Tambah titik</a></x-slot:aksi>
           </x-kosong>
    ikon opsional; nama ikon lihat komponen <x-ikon>.
--}}
@props(['judul', 'aksi' => null, 'ikon' => null])

<div {{ $attributes->class('fl-kosong') }}>
    @if ($ikon)
        <div class="fl-kosong-ikon"><x-ikon :nama="$ikon" /></div>
    @endif
    <div class="fl-kosong-judul">{{ $judul }}</div>
    @if ($slot->isNotEmpty())
        <p>{{ $slot }}</p>
    @endif
    @if ($aksi)
        <div class="fl-kosong-aksi">{{ $aksi }}</div>
    @endif
</div>
