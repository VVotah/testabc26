@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-3">Beri Ulasan Donatur</h4>

    @include('partials.pesan')

    <div class="card">
        <div class="card-body">
            <h5>{{ $pickup->permintaan->listing->nama_makanan ?? 'Makanan' }}</h5>

            <p class="mb-1">
                Donatur:
                {{ $pickup->permintaan->listing->mitra->nama_mitra ?? '-' }}
            </p>

            <p class="mb-3">
                Waktu pickup:
                {{ $pickup->waktu_pickup?->format('d-m-Y H:i') ?? '-' }}
            </p>

            <form action="{{ route('ulasan.store', $pickup->id_jadwal_pickup) }}"
                  method="POST">
                @csrf

                <div class="mb-3">
                    <label for="rating" class="form-label">Rating</label>
                    <select name="rating" id="rating" class="form-select" required>
                        <option value="">Pilih rating</option>
                        @for ($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}" @selected(old('rating') == $i)>
                                {{ $i }} dari 5
                            </option>
                        @endfor
                    </select>
                    @error('rating')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="teks_ulasan" class="form-label">
                        Tulis Ulasan (Opsional)
                    </label>
                    <textarea name="teks_ulasan"
                              id="teks_ulasan"
                              class="form-control"
                              rows="4"
                              placeholder="Bagikan pengalaman kamu...">{{ old('teks_ulasan') }}</textarea>
                    @error('teks_ulasan')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">
                    Kirim Ulasan
                </button>

                <a href="{{ route('ulasan.index') }}" class="btn btn-secondary">
                    Kembali
                </a>
            </form>
        </div>
    </div>
</div>
@endsection
