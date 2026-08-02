@extends('layouts.app')

@php
    /** @var \App\Models\Vehicle $vehicle */
    $gallery = $vehicle->gallery();
    $details = $vehicle->documentedDetails();
    $isSold = $vehicle->status === \App\Enums\VehicleStatus::Sold;
    $isReserved = $vehicle->status === \App\Enums\VehicleStatus::Reserved;
    $specs = array_filter([
        'Año' => (string) $vehicle->year,
        'Kilometraje' => $vehicle->formattedMileage(),
        'Transmisión' => $vehicle->transmission->label(),
        'Combustible' => $vehicle->fuel->label(),
        'Carrocería' => $vehicle->body_type->label(),
        'Motor' => $vehicle->engine_cc ? number_format($vehicle->engine_cc, 0, ',', '.').' cc' : null,
        'Tracción' => $vehicle->traction?->label(),
        'Puertas' => $vehicle->doors ? (string) $vehicle->doors : null,
        'Color' => $vehicle->color,
        'Dueños anteriores' => $vehicle->owners_count ? (string) $vehicle->owners_count : null,
    ]);
@endphp

@section('title', $vehicle->fullTitle().' usado · Pluss Autos')
@section('description', $vehicle->fullTitle().' con '.$vehicle->formattedMileage().'. Inspección completa publicada, con foto de cada detalle. '.$vehicle->formattedPrice().'.')
@section('og_type', 'product')
@section('og_title', $vehicle->fullTitle().' · '.$vehicle->formattedPrice())
@if ($vehicle->coverUrl('full'))
    @section('og_image', $vehicle->coverUrl('full'))
@endif

@push('head')
    <script type="application/ld+json">{!! \App\Support\VehicleSchema::for($vehicle) !!}</script>
@endpush

@section('content')
    <div class="mx-auto max-w-[1120px] px-5 py-8 pb-28 lg:pb-8">
        <nav aria-label="Miga de pan" class="text-sm text-ink-muted">
            <a href="{{ route('vehicles.index') }}" class="hover:text-ink">Autos</a>
            <span aria-hidden="true" class="mx-2">/</span>
            <span>{{ $vehicle->title() }}</span>
        </nav>

        {{-- Galería --}}
        <div class="mt-5" x-data="{ actual: 0 }">
            @if ($gallery->isNotEmpty())
                <figure class="overflow-hidden rounded-[16px] bg-surface">
                    @foreach ($gallery as $index => $media)
                        <img x-show="actual === {{ $index }}"
                             x-transition:enter="transition-opacity duration-300 ease-out-quint"
                             x-transition:enter-start="opacity-0"
                             src="{{ $media->hasGeneratedConversion('full') ? $media->getUrl('full') : $media->getUrl() }}"
                             alt="{{ $vehicle->coverAlt() }}, foto {{ $index + 1 }}"
                             width="1600" height="1200"
                             @if ($index === 0) fetchpriority="high" @else loading="lazy" @endif
                             decoding="async"
                             @class(['aspect-[4/3] w-full object-cover', 'saturate-0 opacity-70' => $isSold])>
                    @endforeach
                </figure>

                @if ($gallery->count() > 1)
                    <ul class="mt-3 flex gap-2 overflow-x-auto pb-1">
                        @foreach ($gallery as $index => $media)
                            <li class="shrink-0">
                                <button type="button" @click="actual = {{ $index }}"
                                        :class="actual === {{ $index }} ? 'ring-2 ring-primary' : 'opacity-70 hover:opacity-100'"
                                        class="block size-20 overflow-hidden rounded-[8px] transition duration-200 ease-out-quint hover:scale-[1.05]">
                                    <span class="sr-only">Ver foto {{ $index + 1 }}</span>
                                    <img src="{{ $media->hasGeneratedConversion('thumb') ? $media->getUrl('thumb') : $media->getUrl() }}"
                                         alt="" loading="lazy" decoding="async" class="size-full object-cover">
                                </button>
                            </li>
                        @endforeach
                    </ul>
                @endif
            @else
                <x-photo-placeholder label="Fotos en camino"
                                     note="Estamos terminando el reportaje fotográfico de este auto. Escríbenos y te las enviamos apenas estén."
                                     class="aspect-[16/7] rounded-[16px]" />
            @endif
        </div>

        <div class="mt-10 grid gap-12 lg:grid-cols-[1fr_320px] lg:gap-14">
            <div>
                <header>
                    @if ($isReserved)
                        <p class="mb-3 inline-block rounded-full bg-accent px-3 py-1 text-sm font-semibold text-ink">Reservado</p>
                    @elseif ($isSold)
                        <p class="mb-3 inline-block rounded-full bg-ink px-3 py-1 text-sm font-semibold text-white">Vendido</p>
                    @endif

                    <h1 class="title-display text-2xl">{{ $vehicle->title() }}</h1>
                    @if ($vehicle->version)
                        <p class="mt-1 text-lg text-ink-muted">{{ $vehicle->version }}</p>
                    @endif

                    <p class="figure-mono mt-5 text-3xl font-semibold">{{ $vehicle->formattedPrice() }}</p>
                </header>

                @if ($vehicle->description)
                    <p class="mt-6 max-w-[68ch] leading-relaxed text-ink-muted">{{ $vehicle->description }}</p>
                @endif

                {{-- Especificaciones --}}
                <section class="mt-12">
                    <h2 class="title-display text-xl">Especificaciones</h2>
                    <dl class="mt-5 grid gap-x-10 border-t border-border sm:grid-cols-2">
                        @foreach ($specs as $label => $value)
                            <div class="flex items-baseline justify-between gap-4 border-b border-border py-3">
                                <dt class="text-ink-muted">{{ $label }}</dt>
                                <dd class="figure-mono font-medium">{{ $value }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </section>

                {{-- Video de recorrido: el comprador ve el auto entero antes de moverse. --}}
                @if ($vehicle->video_url)
                    <section class="mt-12">
                        <h2 class="title-display text-xl">Video del auto</h2>
                        <p class="mt-2 text-ink-muted">Recorrido completo, por dentro y por fuera.</p>
                        <div class="mt-5 aspect-video overflow-hidden rounded-[16px] bg-surface">
                            <iframe src="{{ $vehicle->video_url }}"
                                    title="Video de {{ $vehicle->fullTitle() }}"
                                    class="size-full" loading="lazy"
                                    allow="accelerometer; clipboard-write; encrypted-media; picture-in-picture"
                                    allowfullscreen></iframe>
                        </div>
                    </section>
                @endif

                {{-- Detalles documentados: el componente que distingue al sitio. --}}
                @if ($details->isNotEmpty())
                    <section class="mt-12">
                        <h2 class="title-display text-xl">
                            {{ $details->count() }} {{ \Illuminate\Support\Str::plural('detalle', $details->count()) }}
                            {{ \Illuminate\Support\Str::plural('documentado', $details->count()) }}
                        </h2>
                        <p class="mt-2 max-w-[68ch] leading-relaxed text-ink-muted">
                            Esto es lo que este auto tiene. El precio ya lo considera.
                        </p>

                        <ul class="mt-6 space-y-5">
                            @foreach ($details as $item)
                                <li class="grid gap-4 border-b border-border pb-5 last:border-0 sm:grid-cols-[160px_1fr]">
                                    @if ($item->hasPhoto())
                                        <img src="{{ $item->photoUrl('thumb') }}"
                                             alt="{{ $item->altText() }}"
                                             loading="lazy" decoding="async"
                                             class="aspect-[4/3] w-full rounded-[8px] object-cover sm:w-40">
                                    @else
                                        <x-photo-placeholder compact label="Sin foto de este detalle"
                                                            class="aspect-[4/3] w-full rounded-[8px] sm:w-40" />
                                    @endif

                                    <div>
                                        <p class="flex flex-wrap items-center gap-2">
                                            <span class="font-semibold">{{ $item->label }}</span>
                                            <span @class([
                                                'rounded-full px-2.5 py-0.5 text-xs font-semibold',
                                                'bg-accent text-ink' => $item->status === \App\Enums\InspectionStatus::Observacion,
                                                'bg-primary-soft text-ink' => $item->status === \App\Enums\InspectionStatus::Reparado,
                                            ])>{{ $item->status->label() }}</span>
                                        </p>
                                        <p class="mt-1 text-sm text-ink-muted">{{ $item->category->label() }}</p>
                                        @if ($item->note)
                                            <p class="mt-2 max-w-[62ch] leading-relaxed">{{ $item->note }}</p>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endif

                {{-- Hoja de inspección: se lee como el documento que es. --}}
                @if ($vehicle->hasInspection())
                    <section class="mt-12">
                        <h2 class="title-display text-xl">Hoja de inspección</h2>
                        <p class="mt-2 max-w-[68ch] leading-relaxed text-ink-muted">
                            {{ $vehicle->inspectionItems->count() }} puntos revisados. La misma pauta
                            para todos los autos, para que puedas comparar dos fichas entre sí.
                        </p>

                        <div class="mt-6 space-y-8">
                            @foreach ($vehicle->inspectionByCategory() as $categoryValue => $items)
                                @php $category = \App\Enums\InspectionCategory::from($categoryValue); @endphp
                                <div>
                                    <h3 class="text-sm font-semibold uppercase tracking-wide text-ink-muted">
                                        {{ $category->label() }}
                                    </h3>
                                    <ul class="mt-2 divide-y divide-border border-y border-border">
                                        @foreach ($items as $item)
                                            <li class="flex items-baseline justify-between gap-6 py-2.5">
                                                <span>{{ $item->label }}</span>
                                                <span @class([
                                                    'shrink-0 text-sm',
                                                    'text-ink-muted' => $item->status === \App\Enums\InspectionStatus::Ok,
                                                    'font-semibold text-accent-ink' => $item->status !== \App\Enums\InspectionStatus::Ok,
                                                ])>{{ $item->status->label() }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>

            {{-- Contacto --}}
            <aside class="lg:sticky lg:top-24 lg:self-start">
                @if ($isSold)
                    <div class="rounded-[10px] border border-border p-5">
                        <p class="font-semibold">Este auto ya se vendió</p>
                        <p class="mt-2 text-sm leading-relaxed text-ink-muted">
                            Dejamos la ficha publicada para que veas cómo documentamos cada auto.
                        </p>
                        <x-btn href="{{ route('vehicles.index') }}" block class="mt-5 px-4 py-3">
                            Ver autos disponibles
                        </x-btn>
                    </div>
                @else
                    <div class="rounded-[10px] border border-border p-5">
                        <p class="font-semibold">¿Te interesa este auto?</p>
                        <p class="mt-2 text-sm leading-relaxed text-ink-muted">
                            Escríbenos y coordinamos para que lo pruebes donde a ti te acomode.
                        </p>

                        <x-btn href="{{ $vehicle->whatsappUrl() }}" block class="mt-4">
                            Escribir por WhatsApp
                        </x-btn>

                        <form method="POST" action="{{ route('contact.store') }}" class="mt-6 space-y-3">
                            @csrf
                            <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">
                            <p class="hidden" aria-hidden="true">
                                <label>No llenar<input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                            </p>

                            <x-form.field name="name" id="c-name" label="Nombre" required autocomplete="name" />
                            <x-form.field name="phone" id="c-phone" label="Teléfono" type="tel" required
                                          placeholder="+56 9 1234 5678" autocomplete="tel" />
                            <x-form.textarea name="message" id="c-message" label="Mensaje" :rows="3" />

                            <x-btn type="submit" block class="px-4 py-3">Enviar consulta</x-btn>
                        </form>
                    </div>
                @endif
            </aside>
        </div>

        {{-- Similares --}}
        @if ($similar->isNotEmpty())
            <section class="mt-[clamp(4rem,8vw,7rem)]">
                <h2 class="title-display text-xl">Otros autos parecidos</h2>
                <div class="anim-grid mt-6 grid gap-6 [grid-template-columns:repeat(auto-fit,minmax(280px,1fr))]">
                    @foreach ($similar as $other)
                        <x-vehicle-card :vehicle="$other" style="--i: {{ $loop->index }}" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>

    {{-- Barra fija en móvil: el comprador nunca tiene que buscar cómo escribir. --}}
    @unless ($isSold)
        <div class="fixed inset-x-0 bottom-0 z-sticky border-t border-border bg-bg/95 backdrop-blur-sm lg:hidden">
            <div class="flex items-center justify-between gap-4 px-5 py-3">
                <p class="figure-mono text-lg font-semibold">{{ $vehicle->formattedPrice() }}</p>
                <a href="{{ $vehicle->whatsappUrl() }}"
                   class="rounded-[10px] bg-primary px-5 py-3 font-semibold text-white">
                    WhatsApp
                </a>
            </div>
        </div>
    @endunless
@endsection
