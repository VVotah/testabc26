@extends('layouts.app')

@section('title', 'Detail Notifikasi')

@section('content')
<div class="container-fluid py-4">

    <div class="mb-4">
        <a href="{{ route('notifikasi.index') }}" class="btn btn-outline-secondary btn-sm">
            Kembali
        </a>
    </div>

    @include('partials.pesan')

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <h4 class="mb-3">{{ $notifikasi->jenis_perubahan }}</h4>

            <p class="mb-4">{{ $notifikasi->isi_notifikasi }}</p>

            <hr>

            <p class="text-muted mb-0">
                Waktu:
                {{ $notifikasi->waktu_notifikasi?->format('d M Y, H:i') ?? '-' }}
            </p>

            <form action="{{ route('notifikasi.destroy', $notifikasi->id_notifikasi) }}"
                  method="POST"
                  class="mt-4"
                  onsubmit="return confirm('Yakin ingin menghapus notifikasi ini?')">
                @csrf
                @method('DELETE')

                <button type="submit" class="btn btn-danger">
                    Hapus Notifikasi
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
