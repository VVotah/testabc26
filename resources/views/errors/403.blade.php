@extends(auth()->check() ? 'layouts.app' : 'layouts.tamu')

@section('title', 'Akses ditolak')

@section('content')
    <div class="fl-panel mx-auto mt-4" style="max-width: 520px;">
        <x-kosong judul="Akses ditolak">
            {{ $exception->getMessage() ?: 'Kamu tidak memiliki akses ke halaman ini.' }}
            <x-slot:aksi>
                <a href="{{ url('/') }}" class="btn btn-utama">Kembali ke dashboard</a>
            </x-slot:aksi>
        </x-kosong>
    </div>
@endsection
