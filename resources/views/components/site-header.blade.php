@php
    $settings = \App\Models\SiteSetting::current();
    $links = [
        ['route' => 'vehicles.index', 'label' => 'Autos'],
        ['route' => 'sell.create', 'label' => 'Vende tu auto'],
        ['route' => 'about', 'label' => 'Cómo trabajamos'],
        ['route' => 'contact.create', 'label' => 'Contacto'],
    ];
@endphp

{{-- Encabezado blanco: el logo está diseñado sobre blanco y su palabra
     "Pluss" es negra, de modo que sobre azul desaparecería. El azul de marca
     hace su trabajo en el héroe y en el pie. --}}
{{-- La sombra al desplazarse separa el encabezado del contenido sin necesidad
     de un borde permanente más marcado. --}}
<header x-data="{ open: false, scrolled: false }"
        @scroll.window="scrolled = window.scrollY > 8"
        :class="scrolled ? 'shadow-[0_1px_12px_oklch(0.2_0.022_259_/_0.10)]' : ''"
        class="sticky top-0 z-sticky border-b border-border bg-bg/95 backdrop-blur-sm transition-shadow duration-200 ease-out-quint">
    <div class="mx-auto flex max-w-[1240px] items-center justify-between gap-6 px-5 py-3.5">
        <a href="{{ route('home') }}" class="shrink-0 transition-transform duration-200 ease-out-quint hover:scale-[1.02]">
            <img src="{{ asset('img/logo.webp') }}"
                 alt="Pluss Autos, gestión automotriz"
                 width="600" height="104"
                 class="h-8 w-auto sm:h-9">
        </a>

        <nav aria-label="Principal" class="hidden items-center gap-7 md:flex">
            @foreach ($links as $link)
                <a href="{{ route($link['route']) }}"
                   @class([
                       'link-underline text-sm transition-colors duration-150 hover:text-primary',
                       'font-semibold text-primary' => request()->routeIs($link['route']),
                       'text-ink-muted' => ! request()->routeIs($link['route']),
                   ])
                   @if (request()->routeIs($link['route'])) aria-current="page" @endif>
                    {{ $link['label'] }}
                </a>
            @endforeach
        </nav>

        <a href="{{ $settings->whatsappUrl('Hola, quiero información sobre los autos disponibles.') }}"
           class="hidden rounded-[10px] bg-primary px-4 py-2.5 text-sm font-semibold text-white transition-transform duration-150 ease-out-quint hover:-translate-y-0.5 md:inline-block">
            Escríbenos
        </a>

        <button type="button"
                @click="open = ! open"
                :aria-expanded="open ? 'true' : 'false'"
                aria-controls="menu-movil"
                class="-mr-2 inline-flex size-11 items-center justify-center rounded-[10px] text-ink md:hidden">
            <span class="sr-only">Abrir menú</span>
            <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                <path x-show="! open" d="M4 7h16M4 12h16M4 17h16" stroke-linecap="round"/>
                <path x-show="open" x-cloak d="M6 6l12 12M18 6L6 18" stroke-linecap="round"/>
            </svg>
        </button>
    </div>

    <nav id="menu-movil"
         x-show="open"
         x-cloak
         x-collapse
         aria-label="Principal móvil"
         class="border-t border-border md:hidden">
        <div class="mx-auto max-w-[1240px] px-5 py-3">
            @foreach ($links as $link)
                <a href="{{ route($link['route']) }}"
                   @class([
                       'block border-b border-border py-3.5 last:border-0',
                       'font-semibold text-primary' => request()->routeIs($link['route']),
                   ])>
                    {{ $link['label'] }}
                </a>
            @endforeach
            <a href="{{ $settings->whatsappUrl('Hola, quiero información sobre los autos disponibles.') }}"
               class="mt-3 block rounded-[10px] bg-primary px-4 py-3 text-center font-semibold text-white">
                Escríbenos por WhatsApp
            </a>
        </div>
    </nav>
</header>
