@extends('layouts.app')

@section('title', 'Ubah Kampanye')

@section('content')
<x-kepala-halaman judul="Ubah kampanye" :kembali="route('kampanye.kelola')" teks-kembali="Kembali ke daftar kampanye" />

@include('partials.pesan')

@include('edukasi_dampak.kampanye._form', [
    'aksi' => route('kampanye.update', $kampanye),
    'kampanye' => $kampanye,
])
@endsection