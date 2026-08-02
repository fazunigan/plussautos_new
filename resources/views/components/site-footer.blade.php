@php
    $settings = \App\Models\SiteSetting::current();
@endphp

<footer class="mt-[clamp(4rem,8vw,7rem)] bg-primary-deep text-white/80">
    <div class="mx-auto grid max-w-[1240px] gap-10 px-5 py-14 sm:grid-cols-2 lg:grid-cols-4">
        <div>
            {{-- Versión monocromática blanca: el pie va sobre azul oscuro. --}}
            <img src="{{ asset('img/logo-white.webp') }}"
                 alt="Pluss Autos, gestión automotriz"
                 width="600" height="104" loading="lazy"
                 class="h-8 w-auto">
            <p class="mt-4 max-w-[34ch] text-sm leading-relaxed">
                Compraventa de autos usados. Publicamos la inspección completa de cada auto,
                incluidos los defectos.
            </p>
        </div>

        <div>
            <h2 class="text-sm font-semibold text-white">Comprar</h2>
            <ul class="mt-3 space-y-2.5 text-sm">
                <li><a class="hover:text-white" href="{{ route('vehicles.index') }}">Ver todos los autos</a></li>
                <li><a class="hover:text-white" href="{{ route('vehicles.index', ['carroceria' => 'suv']) }}">SUV disponibles</a></li>
                <li><a class="hover:text-white" href="{{ route('vehicles.index', ['carroceria' => 'camioneta']) }}">Camionetas disponibles</a></li>
            </ul>
        </div>

        <div>
            <h2 class="text-sm font-semibold text-white">Vender</h2>
            <ul class="mt-3 space-y-2.5 text-sm">
                <li><a class="hover:text-white" href="{{ route('sell.create') }}">Vende tu auto</a></li>
                <li><a class="hover:text-white" href="{{ route('about') }}">Cómo trabajamos</a></li>
                <li><a class="hover:text-white" href="{{ route('contact.create') }}">Contacto</a></li>
            </ul>
        </div>

        <div>
            <h2 class="text-sm font-semibold text-white">Contacto</h2>
            <ul class="mt-3 space-y-2.5 text-sm">
                <li>
                    <a class="hover:text-white" href="{{ $settings->whatsappUrl() }}">
                        WhatsApp {{ $settings->whatsapp }}
                    </a>
                </li>
                @if ($settings->email)
                    <li><a class="hover:text-white" href="mailto:{{ $settings->email }}">{{ $settings->email }}</a></li>
                @endif
                @if ($settings->instagram)
                    <li><a class="hover:text-white" href="{{ $settings->instagram }}">Instagram</a></li>
                @endif
            </ul>
        </div>
    </div>

    <div class="border-t border-white/10">
        <div class="mx-auto flex max-w-[1240px] flex-col gap-3 px-5 py-6 text-xs sm:flex-row sm:items-center sm:justify-between">
            <p>&copy; {{ date('Y') }} Pluss Autos</p>
            <div class="flex gap-5">
                <a class="hover:text-white" href="{{ route('terms') }}">Términos</a>
                <a class="hover:text-white" href="{{ route('privacy') }}">Privacidad</a>
            </div>
        </div>
    </div>
</footer>
