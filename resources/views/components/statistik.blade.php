{{--
    Kartu angka ringkasan berikon, bergaya potongan label rak.
    Taruh beberapa di dalam <div class="fl-statistik-baris">.
    Pakai: <x-statistik label="Listing aktif" :angka="$jumlah" ikon="kotak" />
           <x-statistik label="Porsi terselamatkan" :angka="$porsi" ikon="daun" catatan="bulan ini" />
    :angka null = modul pemilik datanya belum dibuat (tampil "Segera hadir", bukan angka palsu).
    Angka 0 tetap tampil 0. Nama ikon: lihat komponen <x-ikon>.
--}}
@props(['label', 'angka' => null, 'catatan' => null, 'ikon' => 'kotak'])

<div {{ $attributes->class('fl-statistik') }}>
    <div class="fl-label">{{ $label }}</div>
    <div class="fl-statistik-ikon"><x-ikon :nama="$ikon" /></div>
    @if (is_null($angka))
        <div class="fl-angka belum">Segera hadir</div>
    @else
        <div class="fl-angka">{{ is_numeric($angka) ? number_format($angka, 0, ',', '.') : $angka }}</div>
    @endif
    @if ($catatan)
        <div class="fl-catatan">{{ $catatan }}</div>
    @endif
</div>
