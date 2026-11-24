{{-- Pesan sekali tampil. Controller: ->with('sukses', '...') atau ->with('galat', '...') --}}
@if (session('sukses'))
    <div class="alert alert-success" role="alert">{{ session('sukses') }}</div>
@endif
@if (session('galat'))
    <div class="alert alert-danger" role="alert">{{ session('galat') }}</div>
@endif
