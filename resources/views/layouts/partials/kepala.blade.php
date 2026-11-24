{{-- Isi <head> bersama untuk layouts.app dan layouts.tamu --}}
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'FoodLoop') - FoodLoop</title>
<link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Azeret+Mono:wght@400;500;600&family=Sofia+Sans:wght@400;500;600;700&family=Sofia+Sans+Extra+Condensed:wght@700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="{{ asset('css/foodloop.css') }}?v={{ filemtime(public_path('css/foodloop.css')) }}" rel="stylesheet">
@stack('styles')
