{{-- Layout halaman sebelum login (masuk, daftar). Milik Nug. --}}
<!DOCTYPE html>
<html lang="id">
<head>
    @include('layouts.partials.kepala')
</head>
<body>
    @yield('content')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @include('layouts.partials.skrip-bersama')
    @stack('scripts')
</body>
</html>
