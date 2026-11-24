
@extends('layouts.app')

@section('content')
    <x-kepala-halaman judul="Forum Sharing">
        <div class="d-flex flex-wrap gap-2">
            {{-- TODO PBI-15: sambungkan ke halaman Aktivitas Forum Saya --}}
            <a class="btn btn-outline-secondary disabled" aria-disabled="true">
                Aktivitas Forum Saya
            </a>

            <a href="{{ route('forum.create') }}" class="btn btn-dark">
                <x-ikon nama="tambah" />Buat Postingan
            </a>
        </div>
    </x-kepala-halaman>

    @include('partials.pesan')

    <div class="d-flex flex-wrap align-items-center justify-content-end gap-2 mb-4">
        <label for="urut" class="mb-0">Urutkan:</label>

        <form method="GET" action="{{ route('forum.index') }}">
            <select name="urut" id="urut" class="form-select w-auto pe-5"
                    onchange="this.form.submit()">
                <option value="terbaru" @selected($urut === 'terbaru')>
                    Terbaru
                </option>
                <option value="terlama" @selected($urut === 'terlama')>
                    Terlama
                </option>
            </select>
        </form>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
        @forelse ($postingan as $p)
            <div class="col">
                <div class="card h-100">
                    @if ($p->foto_url)
                        <img src="{{ $p->foto_url }}"
                             class="card-img-top"
                             alt="{{ $p->nama_makanan }}">
                    @else
                        <div class="ratio ratio-16x9 bg-light d-flex align-items-center justify-content-center text-muted">
                            Foto makanan
                        </div>
                    @endif

                    <div class="card-body">
                        <h2 class="h5 fw-bold">{{ $p->nama_makanan }}</h2>

                        <div class="fw-semibold">
                            Sisa: {{ $p->sisa_porsi }} porsi
                        </div>

                        <div class="text-muted">
                            Lokasi: {{ $p->lokasi_pengambilan }}
                        </div>

                        <div class="text-muted">
                            Batas waktu:
                            {{ \App\Support\Waktu::lengkap($p->batas_waktu) }}
                        </div>
                    </div>

                    <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                        <x-status :nilai="$p->status_postingan" />

                        <a href="{{ route('forum.show', $p) }}"
                           class="btn btn-outline-secondary btn-sm">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-muted">
                Belum ada postingan.
            </div>
        @endforelse
    </div>
@endsection
