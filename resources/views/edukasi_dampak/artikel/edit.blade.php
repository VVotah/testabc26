@extends('layouts.app')

@section('title', 'Ubah Artikel')

@section('content')
<x-kepala-halaman judul="Ubah artikel" :kembali="route('artikel.kelola')" teks-kembali="Kembali ke daftar artikel" />

@include('partials.pesan')

@include('edukasi_dampak.artikel._form', [
    'aksi' => route('artikel.update', $artikel),
    'artikel' => $artikel,
])
@endsection