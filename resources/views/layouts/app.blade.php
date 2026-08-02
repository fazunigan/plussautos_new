<!DOCTYPE html>
<html lang="es-CL">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Pluss Autos · Autos usados revisados y documentados')</title>
    <meta name="description" content="@yield('description', 'Autos usados con inspección publicada: cada defecto documentado con foto. Te llevamos el auto para que lo pruebes donde estés.')">

    <link rel="canonical" href="{{ url()->current() }}">

    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="apple-touch-icon" href="{{ asset('img/apple-touch-icon.png') }}">
    <meta name="theme-color" content="#004AAD">

    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="Pluss Autos">
    <meta property="og:locale" content="es_CL">
    <meta property="og:title" content="@yield('og_title', 'Pluss Autos')">
    <meta property="og:description" content="@yield('description', 'Autos usados con inspección publicada: cada defecto documentado con foto.')">
    <meta property="og:url" content="{{ url()->current() }}">
    @hasSection('og_image')
        <meta property="og:image" content="@yield('og_image')">
        <meta name="twitter:card" content="summary_large_image">
    @else
        <meta property="og:image" content="{{ asset('img/icon-512.png') }}">
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('head')
</head>
<body class="min-h-dvh bg-bg font-sans text-ink antialiased">
    <a href="#contenido"
       class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-toast focus:rounded-lg focus:bg-primary focus:px-4 focus:py-3 focus:text-white">
        Saltar al contenido
    </a>

    <x-site-header />

    <main id="contenido">
        @if (session('status'))
            <div class="mx-auto max-w-[1240px] px-5 pt-6">
                <p role="status"
                   class="rounded-[10px] border border-primary/25 bg-primary-soft px-4 py-3 text-ink">
                    {{ session('status') }}
                </p>
            </div>
        @endif

        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <x-site-footer />
</body>
</html>
