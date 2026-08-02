@props(['vehicle'])

@php
    /** @var \App\Models\Vehicle $vehicle */
    $details = $vehicle->documented_details_count ?? $vehicle->documentedDetailsCount();
    $isSold = $vehicle->status === \App\Enums\VehicleStatus::Sold;
    $isReserved = $vehicle->status === \App\Enums\VehicleStatus::Reserved;
@endphp

<article {{ $attributes->class([
    'group relative rounded-[10px] border border-border bg-bg',
    'transition duration-200 ease-out-quint',
    'hover:-translate-y-1 hover:border-transparent hover:shadow-[0_6px_16px_oklch(0.2_0.022_259_/_0.13)]',
]) }}>
    <div class="relative aspect-[4/3] overflow-hidden rounded-t-[10px] bg-surface">
        @if ($cover = $vehicle->coverUrl('card'))
            <img src="{{ $cover }}"
                 alt="{{ $vehicle->coverAlt() }}"
                 loading="lazy"
                 decoding="async"
                 width="600" height="450"
                 @class([
                     'size-full object-cover transition-transform duration-500 ease-out-quint group-hover:scale-[1.04]',
                     'opacity-60 saturate-0' => $isSold,
                 ])>
        @else
            <x-photo-placeholder label="Fotos en camino" class="size-full" />
        @endif

        {{-- El estado nunca se comunica solo con color: siempre lleva la palabra. --}}
        @if ($isReserved)
            <p class="absolute left-3 top-3 rounded-full bg-accent px-3 py-1 text-xs font-semibold text-ink">
                Reservado
            </p>
        @elseif ($isSold)
            <p class="absolute left-3 top-3 rounded-full bg-ink px-3 py-1 text-xs font-semibold text-white">
                Vendido
            </p>
        @endif
    </div>

    <div class="p-4">
        <h3 class="text-lg font-semibold leading-tight tracking-[-0.015em]">
            <a href="{{ route('vehicles.show', $vehicle) }}" class="after:absolute after:inset-0">
                {{ $vehicle->title() }}
                @if ($vehicle->version)
                    <span class="text-ink-muted">{{ $vehicle->version }}</span>
                @endif
            </a>
        </h3>

        <dl class="mt-3 grid grid-cols-2 gap-x-4 gap-y-1.5 text-sm text-ink-muted">
            <div class="flex items-center gap-1.5">
                <dt class="sr-only">Año</dt>
                <dd class="figure-mono text-ink">{{ $vehicle->year }}</dd>
            </div>
            <div class="flex items-center gap-1.5">
                <dt class="sr-only">Kilometraje</dt>
                <dd class="figure-mono text-ink">{{ number_format($vehicle->mileage_km, 0, ',', '.') }} km</dd>
            </div>
            <div>
                <dt class="sr-only">Transmisión</dt>
                <dd>{{ $vehicle->transmission->label() }}</dd>
            </div>
            <div>
                <dt class="sr-only">Combustible</dt>
                <dd>{{ $vehicle->fuel->label() }}</dd>
            </div>
        </dl>

        {{-- El precio y el contador van en líneas separadas y no enfrentados:
             un precio de ocho cifras en monoespaciada no deja espacio para el
             contador al costado, y el texto terminaba cortado. --}}
        <div class="mt-4 border-t border-border pt-3">
            <p class="figure-mono text-xl font-semibold text-ink">{{ $vehicle->formattedPrice() }}</p>

            @if ($details > 0)
                {{-- El diferenciador de la marca, a la vista en el catálogo. --}}
                <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-accent-ink">
                    <svg class="size-3.5 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5 3a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2H5Zm2.5 4h5a.75.75 0 0 1 0 1.5h-5a.75.75 0 0 1 0-1.5Zm0 3.5h5a.75.75 0 0 1 0 1.5h-5a.75.75 0 0 1 0-1.5Z" clip-rule="evenodd"/>
                    </svg>
                    {{ $details }} {{ \Illuminate\Support\Str::plural('detalle', $details) }}
                    {{ \Illuminate\Support\Str::plural('documentado', $details) }}
                </p>
            @endif
        </div>
    </div>
</article>
