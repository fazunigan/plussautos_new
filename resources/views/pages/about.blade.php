@extends('layouts.app')

@section('title', 'Cómo trabajamos · Pluss Autos')
@section('description', 'Revisamos cada auto en ocho categorías y publicamos el resultado completo, con foto de cada observación.')

@php
    use App\Enums\InspectionCategory;
    use App\Support\InspectionChecklist;

    $total = InspectionChecklist::totalPoints();

    // Geometría de la rueda. Los arcos se calculan acá y no en el navegador:
    // el gráfico queda dibujado en el HTML y se ve aunque el JavaScript no
    // llegue a ejecutarse.
    $radio = 84;
    $circunferencia = 2 * M_PI * $radio;
    $separacion = 3;

    // Ocho tonos del azul de marca, de más oscuro a más claro.
    $tonos = [
        'oklch(0.437 0.171 259.2)',
        'oklch(0.478 0.161 259.2)',
        'oklch(0.519 0.150 259.2)',
        'oklch(0.560 0.139 259.2)',
        'oklch(0.601 0.127 259.2)',
        'oklch(0.642 0.114 259.2)',
        'oklch(0.683 0.100 259.2)',
        'oklch(0.724 0.085 259.2)',
    ];

    $acumulado = 0;
    $segmentos = [];

    foreach (InspectionCategory::cases() as $i => $categoria) {
        $puntos = InspectionChecklist::pointsFor($categoria);
        $largo = count($puntos) / $total * $circunferencia;

        $segmentos[] = [
            'valor' => $categoria->value,
            'etiqueta' => $categoria->label(),
            'n' => count($puntos),
            'puntos' => $puntos,
            'color' => $tonos[$i % count($tonos)],
            'dash' => max($largo - $separacion, 1).' '.($circunferencia - max($largo - $separacion, 1)),
            'offset' => -$acumulado / $total * $circunferencia,
        ];

        $acumulado += count($puntos);
    }

    $paraJs = collect($segmentos)->map(fn ($s) => [
        'valor' => $s['valor'],
        'etiqueta' => $s['etiqueta'],
        'n' => $s['n'],
        'puntos' => $s['puntos'],
        'color' => $s['color'],
    ]);
@endphp

@section('content')
    {{-- Héroe --}}
    <section class="relative overflow-hidden bg-primary text-white">
        <img src="{{ asset('img/mark-white.webp') }}"
             alt="" aria-hidden="true" width="900" height="720" decoding="async"
             class="pointer-events-none absolute -right-16 top-1/2 hidden w-[30rem] -translate-y-1/2 opacity-[0.06] lg:block">

        <div class="relative mx-auto max-w-[1120px] px-5 py-[clamp(3rem,7vw,5rem)]">
            <h1 class="anim-rise title-display max-w-[20ch] text-3xl">
                Cualquiera puede decir que su auto está impecable.
            </h1>
            <p class="anim-rise mt-6 max-w-[60ch] text-lg leading-relaxed text-white/85" style="--i: 1">
                Por eso no lo decimos. Publicamos los {{ $total }} puntos que revisamos en cada
                auto, uno por uno, con lo que encontramos en cada uno.
            </p>
        </div>
    </section>

    {{-- Cifras --}}
    <section class="border-b border-border bg-surface">
        <dl class="mx-auto grid max-w-[1120px] gap-8 px-5 py-[clamp(2.5rem,5vw,3.5rem)] sm:grid-cols-3">
            @foreach ([
                [$total, 'puntos revisados', 'La misma pauta en todos los autos, para que puedas comparar dos fichas entre sí.'],
                [count(InspectionCategory::cases()), 'categorías', 'Del motor a la documentación, sin saltarse ninguna.'],
                [3, 'estados posibles', 'Conforme, con observación o reparado. Nada queda sin marcar.'],
            ] as [$valor, $etiqueta, $detalle])
                <div x-data="contador({{ $valor }})">
                    <dt class="sr-only">{{ $etiqueta }}</dt>
                    <dd class="figure-mono text-[clamp(2.75rem,6vw,3.75rem)] font-semibold leading-none text-primary"
                        x-text="mostrado">{{ $valor }}</dd>
                    <p class="mt-2 font-semibold">{{ $etiqueta }}</p>
                    <p class="mt-1 max-w-[34ch] text-sm leading-relaxed text-ink-muted">{{ $detalle }}</p>
                </div>
            @endforeach
        </dl>
    </section>

    {{-- La rueda de la pauta --}}
    <section class="mx-auto max-w-[1120px] px-5 py-[clamp(3rem,7vw,5rem)]">
        <h2 class="title-display max-w-[24ch] text-2xl">La pauta completa, categoría por categoría.</h2>
        <p class="mt-4 max-w-[62ch] leading-relaxed text-ink-muted">
            Cada porción es una categoría y su tamaño es la cantidad de puntos que revisamos ahí.
            Elige una para ver exactamente qué se mira.
        </p>

        <div x-data='ruedaPauta(@json($paraJs))'
             class="mt-10 grid items-center gap-10 lg:grid-cols-[minmax(0,340px)_1fr] lg:gap-16">

            {{-- El gráfico --}}
            <div class="relative mx-auto w-full max-w-[340px]">
                <svg viewBox="0 0 220 220" class="w-full -rotate-90" role="img"
                     aria-label="Distribución de los {{ $total }} puntos de inspección entre {{ count($segmentos) }} categorías">
                    @foreach ($segmentos as $s)
                        <circle cx="110" cy="110" r="{{ $radio }}"
                                fill="none"
                                stroke="{{ $s['color'] }}"
                                stroke-width="26"
                                stroke-dasharray="{{ $s['dash'] }}"
                                stroke-dashoffset="{{ $s['offset'] }}"
                                class="origin-center cursor-pointer transition-all duration-300 ease-out-quint"
                                :class="esActiva('{{ $s['valor'] }}') ? 'scale-[1.06]' : 'opacity-45'"
                                @click="elegir('{{ $s['valor'] }}')"
                                @keydown.enter="elegir('{{ $s['valor'] }}')"
                                tabindex="0"
                                role="button"
                                aria-label="{{ $s['etiqueta'] }}, {{ $s['n'] }} puntos"></circle>
                    @endforeach
                </svg>

                {{-- Centro de la rueda --}}
                <div class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center text-center">
                    <p class="figure-mono text-4xl font-semibold leading-none"
                       x-text="seleccionada ? seleccionada.n : {{ $total }}">{{ $total }}</p>
                    <p class="mt-1 max-w-[12ch] text-sm leading-tight text-ink-muted"
                       x-text="seleccionada ? seleccionada.etiqueta : 'puntos en total'">puntos en total</p>
                </div>
            </div>

            {{-- Los puntos de la categoría elegida --}}
            <div>
                <div class="flex flex-wrap gap-2">
                    @foreach ($segmentos as $s)
                        <button type="button"
                                @click="elegir('{{ $s['valor'] }}')"
                                :aria-pressed="esActiva('{{ $s['valor'] }}')"
                                class="rounded-full border px-3.5 py-1.5 text-sm transition-all duration-150 ease-out-quint"
                                :class="esActiva('{{ $s['valor'] }}')
                                    ? 'border-primary bg-primary text-white'
                                    : 'border-border text-ink-muted hover:border-ink-muted hover:text-ink'">
                            {{ $s['etiqueta'] }}
                        </button>
                    @endforeach
                </div>

                <ul class="mt-7 space-y-0 divide-y divide-border border-y border-border" aria-live="polite">
                    <template x-for="(punto, i) in (seleccionada ? seleccionada.puntos : [])"
                              :key="activa + '-' + i">
                        <li class="anim-fila flex items-center gap-3 py-3"
                            :style="`animation-delay:${i * 45}ms`">
                            <span class="figure-mono w-6 shrink-0 text-xs text-ink-muted"
                                  x-text="String(i + 1).padStart(2, '0')"></span>
                            <span x-text="punto"></span>
                        </li>
                    </template>
                </ul>

                {{-- Respaldo sin JavaScript: la pauta completa igual queda publicada. --}}
                <noscript>
                    <div class="mt-7 grid gap-6 sm:grid-cols-2">
                        @foreach ($segmentos as $s)
                            <div>
                                <h3 class="font-semibold">{{ $s['etiqueta'] }}</h3>
                                <ul class="mt-2 space-y-1 text-sm text-ink-muted">
                                    @foreach ($s['puntos'] as $punto)
                                        <li>{{ $punto }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                </noscript>
            </div>
        </div>
    </section>

    {{-- Los tres estados --}}
    <section class="bg-surface">
        <div class="mx-auto max-w-[1120px] px-5 py-[clamp(3rem,7vw,5rem)]">
            <h2 class="title-display text-2xl">Los tres estados</h2>
            <p class="mt-4 max-w-[58ch] leading-relaxed text-ink-muted">
                Cada punto de la pauta termina en uno de estos tres. Ninguno queda sin marcar.
            </p>

            <div class="mt-9 grid gap-5 sm:grid-cols-3">
                @foreach ([
                    ['Conforme', 'El punto se revisó y está bien. Sin peros.', 'bg-primary-soft text-primary', 'M16.7 5.3a1 1 0 0 1 0 1.4l-7.5 7.5a1 1 0 0 1-1.4 0L3.3 9.7a1 1 0 1 1 1.4-1.4l3.8 3.8 6.8-6.8a1 1 0 0 1 1.4 0Z'],
                    ['Con observación', 'Encontramos algo. Va con foto y descripción, y el precio lo considera.', 'bg-accent text-ink', 'M10 2a1 1 0 0 1 .87.5l7.5 13A1 1 0 0 1 17.5 17h-15a1 1 0 0 1-.87-1.5l7.5-13A1 1 0 0 1 10 2Zm0 5a.9.9 0 0 0-.9 1l.3 3.2a.6.6 0 0 0 1.2 0l.3-3.2A.9.9 0 0 0 10 7Zm0 6.2a1 1 0 1 0 0 2 1 1 0 0 0 0-2Z'],
                    ['Reparado', 'Tenía un problema y lo arreglamos antes de publicarlo. Te contamos qué era.', 'bg-primary text-white', 'M13.5 2a4.5 4.5 0 0 0-4.2 6.1l-6 6a1.8 1.8 0 0 0 2.6 2.6l6-6A4.5 4.5 0 1 0 13.5 2Z'],
                ] as [$estado, $definicion, $clases, $path])
                    <div class="group rounded-[16px] border border-border bg-bg p-6 transition duration-200 ease-out-quint hover:-translate-y-1 hover:shadow-[0_6px_16px_oklch(0.2_0.022_259_/_0.12)]">
                        <span class="inline-flex size-10 items-center justify-center rounded-full {{ $clases }} transition-transform duration-300 ease-out-quint group-hover:scale-110">
                            <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="{{ $path }}" clip-rule="evenodd"/>
                            </svg>
                        </span>
                        <h3 class="mt-4 text-lg font-semibold">{{ $estado }}</h3>
                        <p class="mt-2 leading-relaxed text-ink-muted">{{ $definicion }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Comparación --}}
    <section class="mx-auto max-w-[1120px] px-5 py-[clamp(3rem,7vw,5rem)]">
        <h2 class="title-display max-w-[26ch] text-2xl">La diferencia con publicar en un clasificado</h2>

        <div class="mt-9 overflow-hidden rounded-[16px] border border-border">
            <div class="grid grid-cols-[1fr_1fr] border-b border-border bg-surface text-sm font-semibold sm:grid-cols-[0.8fr_1fr_1fr]">
                <p class="hidden px-5 py-3.5 sm:block"></p>
                <p class="px-5 py-3.5 text-ink-muted">En un clasificado</p>
                <p class="border-l border-border px-5 py-3.5 text-primary">Con Pluss Autos</p>
            </div>

            @foreach ([
                ['El estado del auto', 'Lo descubres cuando llegas a verlo', 'Publicado punto por punto, con foto de cada detalle'],
                ['El precio', 'Se negocia partiendo de una cifra inflada', 'Ya considera lo que el auto tiene'],
                ['Tu teléfono', 'Queda a la vista de cualquiera', 'Lo vemos solo nosotros'],
                ['La prueba del auto', 'Vas tú hasta donde esté', 'Te lo llevamos donde te acomode'],
                ['Los papeles', 'Los revisas por tu cuenta', 'Van revisados y al día antes de publicar'],
            ] as $i => [$tema, $antes, $ahora])
                <div class="grid grid-cols-[1fr_1fr] border-b border-border last:border-0 sm:grid-cols-[0.8fr_1fr_1fr]">
                    <p class="col-span-2 px-5 pt-4 font-semibold sm:col-span-1 sm:py-4 sm:pt-4">{{ $tema }}</p>
                    <p class="px-5 py-4 text-sm leading-relaxed text-ink-muted">{{ $antes }}</p>
                    <p class="border-l border-border bg-primary-soft/40 px-5 py-4 text-sm leading-relaxed">{{ $ahora }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Revisión precompra: la misma pauta, vendida como servicio suelto. --}}
    <section class="bg-surface">
        <div class="mx-auto grid max-w-[1120px] items-center gap-10 px-5 py-[clamp(3rem,7vw,5rem)] lg:grid-cols-[1fr_0.85fr] lg:gap-16">
            <div>
                <p class="inline-block rounded-full border border-border px-3 py-1 text-sm text-ink-muted">
                    Otro servicio
                </p>
                <h2 class="title-display mt-4 max-w-[22ch] text-2xl">
                    ¿Vas a comprar en otra parte? Lo revisamos igual.
                </h2>
                <p class="mt-5 max-w-[58ch] leading-relaxed text-ink-muted">
                    No hace falta que el auto sea nuestro. Si encontraste uno en un clasificado,
                    en otra automotora o a un particular, vamos a verlo, le aplicamos esta misma
                    pauta de {{ $total }} puntos y te entregamos el informe completo con fotos.
                    Con eso decides si comprarlo, o con qué negociar el precio.
                </p>
                <x-btn href="{{ route('inspection.create') }}" class="mt-7 px-5 py-3">
                    Ver la revisión precompra
                </x-btn>
            </div>

            <ul class="space-y-3">
                @foreach ([
                    'Informe con los '.$total.' puntos y su estado',
                    'Foto de cada hallazgo, de cerca y sin retoque',
                    'Estado de papeles, multas y dueños anteriores',
                    'Estimación de lo que hay que reparar y su urgencia',
                    'Te llega dentro de las 24 horas siguientes',
                ] as $item)
                    <li class="flex items-start gap-3 rounded-[10px] border border-border bg-bg p-4 transition duration-200 ease-out-quint hover:-translate-y-0.5 hover:shadow-[0_4px_12px_oklch(0.2_0.022_259_/_0.10)]">
                        <svg class="mt-0.5 size-5 shrink-0 text-primary" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M16.7 5.3a1 1 0 0 1 0 1.4l-7.5 7.5a1 1 0 0 1-1.4 0L3.3 9.7a1 1 0 1 1 1.4-1.4l3.8 3.8 6.8-6.8a1 1 0 0 1 1.4 0Z" clip-rule="evenodd"/>
                        </svg>
                        <span class="leading-relaxed">{{ $item }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    {{-- Cierre --}}
    <section class="bg-primary-deep text-white">
        <div class="mx-auto flex max-w-[1120px] flex-col items-start justify-between gap-8 px-5 py-[clamp(3rem,6vw,4.5rem)] lg:flex-row lg:items-center">
            <div>
                <h2 class="title-display text-2xl">Mira cómo queda en la práctica</h2>
                <p class="mt-3 max-w-[52ch] leading-relaxed text-white/80">
                    Cada auto del catálogo trae su hoja completa. Ábrela y revísala antes de escribirnos.
                </p>
            </div>
            <div class="flex shrink-0 flex-wrap gap-3">
                <x-btn href="{{ route('vehicles.index') }}" variant="onDarkDeep">Ver autos disponibles</x-btn>
                <x-btn href="{{ route('sell.create') }}" variant="outlineOnDark">Vender mi auto</x-btn>
            </div>
        </div>
    </section>
@endsection
