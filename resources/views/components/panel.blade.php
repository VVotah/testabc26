{{--
    Kotak putih berjudul, dengan tombol aksi di kanan judul (opsional).
    Pakai: <x-panel judul="Daftar titik pengambilan">
               <x-slot:aksi><a href="..." class="btn btn-sekunder btn-sm">+ Tambah titik</a></x-slot:aksi>
               ... isi ...
           </x-panel>
    Tanpa judul: <x-panel> ... </x-panel>
--}}
@props(['judul' => null, 'aksi' => null])

<section {{ $attributes->class('fl-panel') }}>
    @if ($judul)
        <div class="fl-panel-kepala">
            <h2>{{ $judul }}</h2>
            @if ($aksi)
                <div class="d-flex gap-2">{{ $aksi }}</div>
            @endif
        </div>
    @endif
    {{ $slot }}
</section>
