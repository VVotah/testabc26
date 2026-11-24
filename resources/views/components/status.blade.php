{{--
    Badge status dengan warna tetap untuk status baku proyek.
    Pakai: <x-status :nilai="$listing->status_listing" />
    Status yang tidak dikenal tampil netral (abu).
--}}
@props(['nilai'])

@php
    $jenis = match ($nilai) {
        'Aktif', 'Terverifikasi', 'Disetujui', 'Selesai', 'Diserahkan', 'Terbit',
        'Berlangsung', 'Ditindaklanjuti', 'Berhasil' => 'positif',
        'Menunggu', 'Menunggu verifikasi', 'Menunggu Persetujuan', 'Dijadwalkan',
        'Baru', 'Ditinjau', 'Akan Datang', 'Draf' => 'tunggu',
        'Dalam pengantaran' => 'proses',
        'Ditolak', 'Ditangguhkan', 'Gagal' => 'negatif',
        default => 'netral', // Ditutup, Habis, Kedaluwarsa, Dibatalkan, Diarsipkan, dll.
    };
@endphp

<span {{ $attributes->class(['fl-status', $jenis]) }}>{{ $nilai }}</span>
