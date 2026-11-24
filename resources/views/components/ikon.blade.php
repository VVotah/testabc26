{{--
    Ikon garis FoodLoop (satu gaya: garis 1,75, ujung membulat). Warna mengikuti teks.
    Pakai: <x-ikon nama="kotak" />
    Nama yang tersedia: tambah, kotak, masuk, jam, daun, perisai, toko, bendera, rute,
    centang, hati, kalender, lokasi, lonceng, orang, buku, cari, sepeda, unggah, dokumen.
    Butuh ikon lain? Kabari Nug supaya ditambahkan di sini.
--}}
@props(['nama'])

@php
    $bentuk = [
        'tambah' => '<path d="M12 5v14M5 12h14"/>',
        'kotak' => '<path d="M3 8l9-5 9 5v8l-9 5-9-5V8z"/><path d="M3 8l9 5 9-5M12 13v8"/>',
        'masuk' => '<path d="M4 13l2.5-8h11L20 13v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-5z"/><path d="M4 13h5a3 3 0 0 0 6 0h5"/>',
        'jam' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
        'daun' => '<path d="M5 19C5 11 10 6 19 5c0 9-4 14-12 14"/><path d="M5 19c2-4 5-7 9-9"/>',
        'perisai' => '<path d="M12 3l8 3v6c0 4.5-3.2 7.8-8 9-4.8-1.2-8-4.5-8-9V6l8-3z"/><path d="M9 12l2 2 4-4"/>',
        'toko' => '<path d="M4 10l1.5-5h13L20 10"/><path d="M4 10a2.7 2.7 0 0 0 5.3 0 2.7 2.7 0 0 0 5.4 0 2.7 2.7 0 0 0 5.3 0v9H4v-9z"/><path d="M10 19v-5h4v5"/>',
        'bendera' => '<path d="M5 21V4"/><path d="M5 5h11l-2 4 2 4H5"/>',
        'rute' => '<circle cx="6" cy="18" r="2"/><circle cx="18" cy="6" r="2"/><path d="M8 18h6a3 3 0 0 0 0-6h-4a3 3 0 0 1 0-6h6"/>',
        'centang' => '<circle cx="12" cy="12" r="9"/><path d="M8 12.5l2.5 2.5L16 9.5"/>',
        'hati' => '<path d="M12 20s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.6-7 10-7 10z"/>',
        'kalender' => '<rect x="4" y="5" width="16" height="15" rx="2"/><path d="M4 10h16M8 3v4M16 3v4"/>',
        'lokasi' => '<path d="M12 21s-6.5-6-6.5-11a6.5 6.5 0 0 1 13 0c0 5-6.5 11-6.5 11z"/><circle cx="12" cy="10" r="2.5"/>',
        'lonceng' => '<path d="M6 16V11a6 6 0 0 1 12 0v5l1.5 2h-15L6 16z"/><path d="M10 20a2 2 0 0 0 4 0"/>',
        'orang' => '<circle cx="12" cy="8" r="3.5"/><path d="M5 20a7 7 0 0 1 14 0"/>',
        'buku' => '<path d="M5 4h10a3 3 0 0 1 3 3v13H8a3 3 0 0 1-3-3V4z"/><path d="M5 17a3 3 0 0 1 3-3h10"/>',
        'sepeda' => '<circle cx="6" cy="17" r="3.5"/><circle cx="18" cy="17" r="3.5"/><path d="M6 17l4-8h5l3 8M10 9L8.5 6H7M15 9l-2 8"/>',
        'unggah' => '<path d="M12 15V4M7.5 8.5L12 4l4.5 4.5"/><path d="M4 15v3a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-3"/>',
        'dokumen' => '<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5M9 13h6M9 17h4"/>',
        'cari' => '<circle cx="11" cy="11" r="6.5"/><path d="M16 16l4 4"/>',
    ];
@endphp

<svg {{ $attributes->class('fl-ikon') }} viewBox="0 0 24 24" aria-hidden="true" focusable="false">{!! $bentuk[$nama] ?? $bentuk['kotak'] !!}</svg>
