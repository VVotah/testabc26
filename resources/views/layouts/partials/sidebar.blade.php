@php
    $menuSidebar = collect(\App\Support\MenuSidebar::untuk(auth()->user()));
    $menuTersedia = $menuSidebar->filter(fn ($item) => $item['url']);
    $menuSegera = $menuSidebar->reject(fn ($item) => $item['url']);
@endphp

<aside class="fl-sidebar" id="sidebar" aria-label="Menu utama">
    <a href="{{ route('dashboard') }}" class="fl-merek">
        <img src="{{ asset('img/logo.png') }}" alt="FoodLoop" width="132" height="44">
    </a>

    <ul class="fl-menu">
        @foreach ($menuTersedia as $item)
            <li>
                <a href="{{ $item['url'] }}" @class(['aktif' => $item['aktif']])
                    @if ($item['aktif']) aria-current="page" @endif>{{ $item['label'] }}</a>
            </li>
        @endforeach
    </ul>

    {{-- Menu yang modulnya belum tersedia dikumpulkan di bawah satu keterangan. --}}
    @if ($menuSegera->isNotEmpty())
        <div class="fl-menu-segera">
            <div class="fl-cap">Segera hadir</div>
            <ul class="fl-menu">
                @foreach ($menuSegera as $item)
                    <li><span aria-disabled="true">{{ $item['label'] }}</span></li>
                @endforeach
            </ul>
        </div>
    @endif
</aside>
<button type="button" class="fl-tirai" id="tiraiSidebar" aria-label="Tutup menu" tabindex="-1"></button>
