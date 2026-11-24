@extends('layouts.app')

@section('title', 'Tulis Artikel')

@section('content')
<x-kepala-halaman judul="Tulis artikel" :kembali="route('artikel.kelola')" teks-kembali="Kembali ke daftar artikel" />

@include('partials.pesan')

@include('edukasi_dampak.artikel._form', ['aksi' => route('artikel.store')])
@endsection