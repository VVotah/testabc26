@extends('layouts.app')

@section('title', 'Buat Kampanye')

@section('content')
<x-kepala-halaman judul="Buat kampanye" :kembali="route('kampanye.kelola')" teks-kembali="Kembali ke daftar kampanye" />

@include('partials.pesan')

@include('edukasi_dampak.kampanye._form', ['aksi' => route('kampanye.store')])
@endsection