{{--
    Layout bersama FoodLoop (milik Nug). Dipakai semua halaman setelah login.

    Cara pakai di view modul:
        @extends('layouts.app')
        @section('title', 'Judul tab browser')      (opsional)
        @section('content') ... @endsection
        @push('scripts') ... @endpush                (opsional, untuk JS halaman)

    Komponen siap pakai (kepala halaman, panel, statistik, status, kosong, pesan):
    lihat CLAUDE.md bagian "Komponen tampilan" atau buka /komponen di lokal.
--}}
<!DOCTYPE html>
<html lang="id">
<head>
    @include('layouts.partials.kepala')
</head>
<body>
    <a href="#konten" class="fl-lewati">Lewati ke konten</a>

    @include('layouts.partials.sidebar')

    <div class="fl-utama">
        @include('layouts.partials.topbar')

        <main class="fl-konten" id="konten" tabindex="-1">
            @yield('content')
        </main>
    </div>

    @include('layouts.partials.modal-keluar')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @include('layouts.partials.skrip-bersama')
    <script>
        // Buka/tutup sidebar di layar kecil.
        (function () {
            const sidebar = document.getElementById('sidebar');
            const tombol = document.getElementById('tombolMenu');
            const atur = function (buka) {
                sidebar.classList.toggle('terbuka', buka);
                if (tombol) tombol.setAttribute('aria-expanded', buka);
            };
            if (tombol) tombol.addEventListener('click', function () { atur(!sidebar.classList.contains('terbuka')); });
            document.getElementById('tiraiSidebar').addEventListener('click', function () { atur(false); });
            document.addEventListener('keydown', function (e) { if (e.key === 'Escape') atur(false); });
        })();

        // Jam di topbar mengikuti waktu perangkat.
        (function () {
            const jam = document.getElementById('jamSekarang');
            if (!jam) return;
            const perbarui = function () {
                const s = new Date();
                jam.textContent = String(s.getHours()).padStart(2, '0') + '.' + String(s.getMinutes()).padStart(2, '0');
            };
            perbarui();
            setInterval(perbarui, 20000);
        })();
    </script>
    @stack('scripts')
</body>
</html>
