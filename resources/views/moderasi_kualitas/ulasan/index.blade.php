@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-3">Ulasan Donatur</h4>

    @include('partials.pesan')

    @if ($pickup->isEmpty())
        <div class="alert alert-info">
            Belum ada pickup selesai yang perlu diberi ulasan.
        </div>
    @else
        <div class="row">
            @foreach ($pickup as $item)
                <div class="col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">
                                {{ $item->permintaan->listing->nama_makanan ?? 'Makanan' }}
                            </h5>

                            <p class="mb-1">
                                Donatur:
                                {{ $item->permintaan->listing->mitra->nama_mitra ?? '-' }}
                            </p>

                            <p class="mb-1">
                                Waktu pickup:
                                {{ $item->waktu_pickup?->format('d-m-Y H:i') ?? '-' }}
                            </p>

                            <p class="mb-3">
                                Status:
                                {{ $item->status_pickup }}
                            </p>

                            <a href="{{ route('ulasan.create', $item->id_jadwal_pickup) }}"
                               class="btn btn-primary">
                                Beri Ulasan
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
