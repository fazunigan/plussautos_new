@extends('layouts.app')

@section('title', 'Revisión precompra · Pluss Autos')
@section('description', 'Encontraste un auto y quieres saber cómo está de verdad antes de comprarlo. Lo revisamos con la misma pauta de '.\App\Support\InspectionChecklist::totalPoints().' puntos que aplicamos a nuestro stock y te entregamos el informe completo con fotos.')

@php
    use App\Enums\InspectionCategory;
    use App\Support\InspectionChecklist;

    $total = InspectionChecklist::totalPoints();
@endphp

@section('content')
    {{-- Héroe --}}
    <section class="relative overflow-hidden bg-primary text-white">
        <img src="{{ asset('img/mark-white.webp') }}"
             alt="" aria-hidden="true" width="900" height="720" decoding="async"
             class="pointer-events-none absolute -right-16 top-1/2 hidden w-[28rem] -translate-y-1/2 opacity-[0.06] lg:block">

        <div class="relative mx-auto max-w-[1120px] px-5 py-[clamp(3rem,7vw,5rem)]">
            <p class="anim-rise inline-block rounded-full border border-white/30 px-3 py-1 text-sm">
                Servicio
            </p>
            <h1 class="anim-rise title-display mt-4 max-w-[22ch] text-3xl" style="--i: 1">
                Encontraste un auto. Nosotros te decimos cómo está de verdad.
            </h1>
            <p class="anim-rise mt-6 max-w-[58ch] text-lg leading-relaxed text-white/85" style="--i: 2">
                No importa dónde lo hayas encontrado ni a quién se lo compres. Vamos, lo revisamos
                con la misma pauta de {{ $total }} puntos que aplicamos a nuestro propio stock y te
                entregamos el informe completo, con fotos de cada hallazgo.
            </p>
            <p class="anim-rise mt-6 text-sm text-white/70" style="--i: 3">
                Un auto usado mal comprado cuesta millones. Una revisión, una fracción de eso.
            </p>
        </div>
    </section>

    <div class="mx-auto max-w-[1120px] px-5 py-[clamp(3rem,6vw,4.5rem)]">
        <div class="grid gap-12 lg:grid-cols-[1fr_420px] lg:gap-16">
            <div>
                {{-- Qué incluye --}}
                <section>
                    <h2 class="title-display text-2xl">Qué revisamos</h2>
                    <p class="mt-4 max-w-[60ch] leading-relaxed text-ink-muted">
                        Las mismas ocho categorías, punto por punto. No es una mirada rápida ni una
                        opinión: es la pauta completa, con el estado de cada punto.
                    </p>

                    <div x-data="{ abierta: 'motor' }" class="mt-8 divide-y divide-border border-y border-border">
                        @foreach (InspectionCategory::cases() as $categoria)
                            @php $puntos = InspectionChecklist::pointsFor($categoria); @endphp
                            <div>
                                <button type="button"
                                        @click="abierta = abierta === '{{ $categoria->value }}' ? null : '{{ $categoria->value }}'"
                                        :aria-expanded="abierta === '{{ $categoria->value }}'"
                                        class="flex w-full items-center justify-between gap-4 py-4 text-left">
                                    <span class="flex items-baseline gap-3">
                                        <span class="figure-mono text-sm text-primary">
                                            {{ str_pad((string) $categoria->order(), 2, '0', STR_PAD_LEFT) }}
                                        </span>
                                        <span class="font-medium">{{ $categoria->label() }}</span>
                                    </span>
                                    <span class="flex shrink-0 items-center gap-3">
                                        <span class="figure-mono text-sm text-ink-muted">{{ count($puntos) }}</span>
                                        <svg class="size-5 text-ink-muted transition-transform duration-200 ease-out-quint"
                                             :class="abierta === '{{ $categoria->value }}' ? 'rotate-45' : ''"
                                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                            <path d="M12 5v14M5 12h14" stroke-linecap="round"/>
                                        </svg>
                                    </span>
                                </button>

                                <ul x-show="abierta === '{{ $categoria->value }}'" x-collapse class="pb-4">
                                    @foreach ($puntos as $i => $punto)
                                        <li class="flex items-center gap-3 py-1.5 text-sm text-ink-muted">
                                            <svg class="size-4 shrink-0 text-primary" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M16.7 5.3a1 1 0 0 1 0 1.4l-7.5 7.5a1 1 0 0 1-1.4 0L3.3 9.7a1 1 0 1 1 1.4-1.4l3.8 3.8 6.8-6.8a1 1 0 0 1 1.4 0Z" clip-rule="evenodd"/>
                                            </svg>
                                            {{ $punto }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                </section>

                {{-- Qué recibes --}}
                <section class="mt-[clamp(2.5rem,5vw,3.5rem)]">
                    <h2 class="title-display text-2xl">Qué recibes</h2>

                    <div class="mt-7 grid gap-5 sm:grid-cols-2">
                        @foreach ([
                            ['Informe punto por punto', 'Los '.$total.' puntos con su estado: conforme, con observación o para reparar.'],
                            ['Foto de cada hallazgo', 'De cerca y sin retoque. Lo que veas en el informe es lo que tiene el auto.'],
                            ['Estado de los papeles', 'Permiso de circulación, revisión técnica, multas y número de dueños anteriores.'],
                            ['Estimación de lo que hay que reparar', 'Qué necesita atención, con qué urgencia y cuánto cuesta aproximadamente.'],
                        ] as [$titulo, $texto])
                            <div class="group rounded-[16px] border border-border p-5 transition duration-200 ease-out-quint hover:-translate-y-1 hover:shadow-[0_6px_16px_oklch(0.2_0.022_259_/_0.12)]">
                                <h3 class="font-semibold">{{ $titulo }}</h3>
                                <p class="mt-2 text-sm leading-relaxed text-ink-muted">{{ $texto }}</p>
                            </div>
                        @endforeach
                    </div>
                </section>

                {{-- Cómo funciona --}}
                <section class="mt-[clamp(2.5rem,5vw,3.5rem)]">
                    <h2 class="title-display text-2xl">Cómo funciona</h2>

                    <ol class="mt-8 space-y-7">
                        @foreach ([
                            ['Nos cuentas cuál es el auto', 'Marca, modelo, año y dónde está. Si tienes el enlace de la publicación, mejor todavía.'],
                            ['Coordinamos con el vendedor', 'Tú no tienes que estar. Vamos donde esté el auto en el horario que acuerden.'],
                            ['Revisamos los '.$total.' puntos', 'Con la misma pauta de siempre, más la prueba de manejo y la revisión de los papeles.'],
                            ['Te llega el informe', 'Dentro de las 24 horas siguientes, con fotos y con nuestra recomendación en una línea.'],
                        ] as $index => [$titulo, $texto])
                            <li class="flex gap-5">
                                <span class="figure-mono shrink-0 text-sm text-primary">0{{ $index + 1 }}</span>
                                <div>
                                    <h3 class="font-semibold">{{ $titulo }}</h3>
                                    <p class="mt-1 max-w-[56ch] leading-relaxed text-ink-muted">{{ $texto }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </section>

                <div class="mt-[clamp(2.5rem,5vw,3.5rem)] rounded-[16px] bg-surface p-6">
                    <h2 class="font-semibold">¿Y si el auto resulta estar malo?</h2>
                    <p class="mt-2 max-w-[58ch] leading-relaxed text-ink-muted">
                        Mejor saberlo antes. El informe te sirve igual: para no comprarlo, o para
                        negociar el precio con el detalle en la mano. Si prefieres, te mostramos
                        qué tenemos disponible en el catálogo.
                    </p>
                </div>
            </div>

            {{-- Formulario --}}
            <div class="lg:sticky lg:top-24 lg:self-start">
                <div class="rounded-[16px] border border-border p-6">
                    <h2 class="text-lg font-semibold">Pide la revisión</h2>
                    <p class="mt-1.5 text-sm text-ink-muted">
                        Te confirmamos el valor y coordinamos la visita.
                    </p>

                    <form method="POST" action="{{ route('inspection.store') }}" class="mt-6 space-y-4">
                        @csrf
                        <p class="hidden" aria-hidden="true">
                            <label>No llenar<input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                        </p>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <x-form.field name="name" id="rev-name" label="Nombre" required autocomplete="name" />
                            <x-form.field name="phone" id="rev-phone" label="Teléfono" type="tel" required
                                          placeholder="+56 9 1234 5678" autocomplete="tel" />
                        </div>

                        <x-form.field name="email" id="rev-email" label="Correo" type="email" optional
                                      hint="Por acá te enviamos el informe." autocomplete="email" />

                        <div class="grid gap-4 sm:grid-cols-2">
                            <x-form.field name="t_brand" id="rev-brand" label="Marca" required placeholder="Mazda" />
                            <x-form.field name="t_model" id="rev-model" label="Modelo" required placeholder="CX-5" />
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <x-form.field name="t_year" id="rev-year" label="Año" type="number" mono required
                                          min="1950" max="{{ date('Y') + 1 }}" />
                            <x-form.field name="t_plate" id="rev-plate" label="Patente" optional mono />
                        </div>

                        <x-form.field name="t_comuna" id="rev-comuna" label="Comuna donde está el auto" required
                                      placeholder="Ñuñoa" />

                        <x-form.field name="t_listing_url" id="rev-url" label="Enlace de la publicación" type="url" optional
                                      placeholder="https://..."
                                      hint="Si lo viste publicado en algún lado, pégalo acá." />

                        <x-form.textarea name="message" id="rev-message" label="Algo que quieras contarnos" optional :rows="3" />

                        <x-btn type="submit" block class="px-4 py-3.5">Pedir la revisión</x-btn>

                        <p class="text-xs leading-relaxed text-ink-muted">
                            Usamos tus datos solo para coordinar esta revisión. Puedes leer cómo los
                            tratamos en <a class="underline underline-offset-2" href="{{ route('privacy') }}">privacidad</a>.
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
