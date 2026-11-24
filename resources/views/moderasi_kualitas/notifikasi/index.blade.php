@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Notifikasi</h2>
            <p class="text-muted mb-0">Informasi terbaru mengenai aktivitas FoodLoop kamu.</p>
        </div>
    </div>

    @include('partials.pesan')

    @forelse ($notifikasi as $item)
        <div class="card mb-3 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-start gap-3">
                <div>
                    <h6 class="fw-bold mb-2">
                        {{ $item->jenis_perubahan }}
                    </h6>

                    <p class="mb-2">
                        {{ $item->isi_notifikasi }}
                    </p>

                    <small class="text-muted">
                        {{ $item->waktu_notifikasi?->format('d M Y, H:i') ?? '-' }}
                    </small>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('notifikasi.show', $item->id_notifikasi) }}"
                       class="btn btn-sm btn-outline-primary">
                        Detail
                    </a>

                    <form action="{{ route('notifikasi.destroy', $item->id_notifikasi) }}"
                          method="POST"
                          onsubmit="return confirm('Yakin ingin menghapus notifikasi ini?')">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn btn-sm btn-outline-danger">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <h5>Belum Ada Notifikasi</h5>
                <p class="text-muted mb-0">
                    Notifikasi aktivitas FoodLoop akan muncul di halaman ini.
                </p>
            </div>
        </div>
    @endforelse

</div>
@endsection
