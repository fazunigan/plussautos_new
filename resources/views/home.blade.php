@extends('layouts.app')

@section('title', 'Pluss Autos · Compra y venta de autos usados')
@section('description', 'Autos usados revisados y con los papeles al día. Elige del catálogo, prueba el auto donde te acomode y llévatelo con la transferencia hecha el mismo día.')

@section('content')
    {{-- Héroe. Vende autos, no vende el método: lo que va arriba es el producto
         y la entrada al catálogo. La inspección publicada es un respaldo del
         argumento, no el argumento, y tiene su propia sección más abajo. --}}
    <section class="relative overflow-hidden bg-primary text-white">
        <div class="relative mx-auto grid max-w-[1240px] items-center gap-10 px-5 py-[clamp(3rem,7vw,5rem)] lg:grid-cols-[1.1fr_400px] lg:gap-14">
            {{-- Única coreografía de carga del sitio: primer pliegue y nada más. --}}
            <div>
                <h1 class="anim-rise title-display text-3xl">
                    Tu próximo auto, revisado y con los papeles al día.
                </h1>

                <p class="anim-rise mt-6 max-w-[52ch] text-lg leading-relaxed text-white/85" style="--i: 1">
                    Elige del catálogo, pruébalo donde te acomode y llévatelo con la
                    transferencia hecha el mismo día. También compramos el tuyo.
                </p>

                <div class="anim-rise mt-9 flex flex-wrap gap-3" style="--i: 2">
                    <x-btn href="{{ route('vehicles.index') }}" variant="onDark">
                        Ver autos disponibles
                    </x-btn>
                    {{-- Ancla y no enlace a otra página: el cotizador está más
                         abajo en esta misma portada. --}}
                    <x-btn href="#cotizador" variant="outlineOnDark">
                        Vender mi auto
                    </x-btn>
                </div>

                <ul class="anim-rise mt-9 flex flex-wrap gap-x-7 gap-y-2 text-sm text-white/75" style="--i: 3">
                    @foreach ([
                        'Inspección de '.\App\Support\InspectionChecklist::totalPoints().' puntos publicada',
                        'Prueba a domicilio',
                        'Transferencia digital',
                    ] as $punto)
                        <li class="flex items-center gap-2">
                            <svg class="size-4 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M16.7 5.3a1 1 0 0 1 0 1.4l-7.5 7.5a1 1 0 0 1-1.4 0L3.3 9.7a1 1 0 1 1 1.4-1.4l3.8 3.8 6.8-6.8a1 1 0 0 1 1.4 0Z" clip-rule="evenodd"/>
                            </svg>
                            {{ $punto }}
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- El buscador ocupa la segunda columna siempre: es más útil que una
                 foto y no depende de que haya fotos cargadas. --}}
            <div class="anim-fade" style="--i: 2">
                <x-quick-search :brands="$brands" :available-count="$availableCount" />
            </div>
        </div>
    </section>

    {{-- Últimos ingresos --}}
    @if ($latest->isNotEmpty())
        <section class="mx-auto max-w-[1240px] px-5 py-[clamp(4rem,8vw,7rem)]">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <h2 class="title-display text-2xl">Últimos ingresos</h2>
                <a href="{{ route('vehicles.index') }}" class="text-sm font-semibold text-primary underline underline-offset-4">
                    Ver todo el catálogo
                </a>
            </div>

            <div class="anim-grid mt-8 grid gap-6 [grid-template-columns:repeat(auto-fit,minmax(300px,1fr))]">
                @foreach ($latest as $vehicle)
                    <x-vehicle-card :vehicle="$vehicle" style="--i: {{ $loop->index }}" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- En vez de describir qué revisamos, se muestra la hoja de inspección de
         un auto que está en el catálogo ahora mismo. El documento convence más
         que la promesa de que existe el documento. --}}
    @if ($sample && $sampleRows->isNotEmpty())
        <section class="bg-surface">
            <div class="mx-auto grid max-w-[1240px] gap-12 px-5 py-[clamp(4rem,8vw,7rem)] lg:grid-cols-[0.82fr_1fr] lg:gap-16">
                <div class="lg:pt-6">
                    <h2 class="title-display max-w-[18ch] text-2xl">
                        Así se ve la hoja de un auto real.
                    </h2>
                    <p class="mt-5 max-w-[58ch] leading-relaxed text-ink-muted">
                        No es un resumen ni un sello de garantía. Son los
                        {{ $sampleRows->count() }} puntos que revisamos, con el estado de cada uno,
                        tal como quedan publicados en la ficha. Esta es la de un
                        {{ $sample->title() }} que está en el catálogo ahora.
                    </p>

                    <dl class="mt-8 flex flex-wrap gap-x-10 gap-y-5">
                        @foreach ([
                            [$sampleRows->count(), 'puntos revisados'],
                            [\App\Enums\InspectionCategory::cases() ? count(\App\Enums\InspectionCategory::cases()) : 0, 'categorías'],
                            [$sample->documentedDetailsCount(), 'con observación'],
                        ] as [$valor, $etiqueta])
                            <div>
                                <dt class="sr-only">{{ $etiqueta }}</dt>
                                <dd class="figure-mono text-2xl font-semibold leading-none">{{ $valor }}</dd>
                                <p class="mt-1.5 text-sm text-ink-muted">{{ $etiqueta }}</p>
                            </div>
                        @endforeach
                    </dl>

                    <div class="mt-9 flex flex-wrap gap-3">
                        <x-btn href="{{ route('vehicles.show', $sample) }}" class="px-5 py-3">
                            Ver esta ficha completa
                        </x-btn>
                        <x-btn href="{{ route('about') }}" variant="outline" class="px-5 py-3">
                            Cómo trabajamos
                        </x-btn>
                    </div>
                </div>

                {{-- La hoja. Se ve como el documento que es: cabecera, categorías
                     navegables y filas con su estado. --}}
                <div x-data='hojaInspeccion(@json($sampleRows))'
                     class="overflow-hidden rounded-[16px] border border-border bg-bg shadow-[0_1px_2px_oklch(0.2_0.022_259_/_0.06)]">
                    <div class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-1 border-b border-border px-5 py-4">
                        <p class="font-semibold">{{ $sample->fullTitle() }}</p>
                        <p class="figure-mono text-xs text-ink-muted">Hoja de inspección</p>
                    </div>

                    <div class="fade-r flex gap-2 overflow-x-auto border-b border-border px-5 py-3">
                        <template x-for="c in categorias" :key="c.valor">
                            <button type="button"
                                    @click="elegir(c.valor)"
                                    :aria-pressed="cat === c.valor"
                                    class="shrink-0 rounded-full border px-3 py-1.5 text-sm transition-colors duration-150 ease-out-quint"
                                    :class="cat === c.valor
                                        ? 'border-primary bg-primary text-white'
                                        : 'border-border text-ink-muted hover:border-ink-muted hover:text-ink'"
                                    x-text="c.etiqueta"></button>
                        </template>
                    </div>

                    <ul class="fade-b max-h-[26rem] divide-y divide-border overflow-y-auto" aria-live="polite">
                        <template x-for="(fila, i) in visibles" :key="cat + '-' + i">
                            <li class="anim-fila px-5 py-3" :style="`animation-delay:${Math.min(i * 35, 280)}ms`">
                                <div class="flex items-baseline justify-between gap-4">
                                    <span class="text-sm" x-text="fila.label"></span>
                                    <span class="shrink-0 rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                          :class="fila.status === 'ok'
                                              ? 'bg-primary-soft text-primary'
                                              : 'bg-accent text-ink'"
                                          x-text="fila.statusLabel"></span>
                                </div>
                                <p x-show="fila.note" x-cloak
                                   class="mt-1 max-w-[58ch] text-sm leading-relaxed text-ink-muted"
                                   x-text="fila.note"></p>
                            </li>
                        </template>
                    </ul>

                    <p class="border-t border-border px-5 py-3 text-xs text-ink-muted">
                        <span class="figure-mono" x-text="visibles.length"></span>
                        de <span class="figure-mono" x-text="filas.length"></span> puntos
                        <span x-show="cat !== 'todas'" x-cloak>en esta categoría</span>
                    </p>
                </div>
            </div>
        </section>
    @endif

    {{-- Te lo llevamos. Los números están justificados: es una secuencia real. --}}
    <section class="mx-auto max-w-[1240px] px-5 py-[clamp(4rem,8vw,7rem)]">
        {{-- La misma pauta, ofrecida como servicio suelto a quien compra afuera. --}}
        <div class="mb-[clamp(4rem,8vw,7rem)] flex flex-col items-start justify-between gap-6 rounded-[16px] border border-border p-7 transition duration-200 ease-out-quint hover:shadow-[0_6px_16px_oklch(0.2_0.022_259_/_0.10)] lg:flex-row lg:items-center">
            <div>
                <h2 class="text-xl font-semibold">¿El auto que te gustó no es nuestro?</h2>
                <p class="mt-2 max-w-[62ch] leading-relaxed text-ink-muted">
                    Lo revisamos igual. Vamos a verlo donde esté, le aplicamos la misma pauta de
                    {{ \App\Support\InspectionChecklist::totalPoints() }} puntos y te entregamos el
                    informe completo con fotos antes de que lo compres.
                </p>
            </div>
            <x-btn href="{{ route('inspection.create') }}" variant="outline" class="shrink-0 px-5 py-3">
                Revisión precompra
            </x-btn>
        </div>

        <h2 class="title-display max-w-[22ch] text-2xl">
            Te llevamos el auto donde estés.
        </h2>

        <ol class="mt-10 grid gap-8 sm:grid-cols-3">
            @foreach ([
                ['Eliges el auto en el catálogo', 'La ficha trae fotos, video de recorrido y la inspección completa. Decides antes de mover un dedo.'],
                ['Coordinamos dónde probarlo', 'Tu casa, tu trabajo o donde prefieras. Vamos con el auto y con los papeles.'],
                ['Cierras si te convence', 'Transferencia digital y entrega el mismo día. Si no te convence, no pasa nada.'],
            ] as $index => [$title, $body])
                <li>
                    <p class="figure-mono text-sm text-primary">0{{ $index + 1 }}</p>
                    <h3 class="mt-2 text-lg font-semibold">{{ $title }}</h3>
                    <p class="mt-2 leading-relaxed text-ink-muted">{{ $body }}</p>
                </li>
            @endforeach
        </ol>
    </section>

    {{-- Cotizador. Va completo en la portada y no como un enlace a otra página:
         el que llega a vender no vuelve si primero tiene que navegar. --}}
    <section id="cotizador" class="bg-primary-deep text-white scroll-mt-4">
        <div class="mx-auto grid max-w-[1240px] gap-10 px-5 py-[clamp(3rem,7vw,5rem)] lg:grid-cols-[1fr_520px] lg:items-center lg:gap-16">
            <div>
                <h2 class="title-display text-2xl">Cotiza tu auto online en minutos</h2>
                <p class="mt-4 max-w-[56ch] text-lg leading-relaxed text-white/85">
                    Completas tres pasos y te respondemos con una oferta concreta dentro del
                    día hábil siguiente. Sin publicar tu teléfono ni recibir desconocidos
                    en tu casa.
                </p>

                <ul class="mt-8 space-y-3">
                    @foreach ([
                        'Te decimos un rango realista, no una cifra inflada para engancharte',
                        'Revisamos el auto donde a ti te acomode',
                        'Pago inmediato por transferencia, o consignación si prefieres',
                    ] as $punto)
                        <li class="flex items-start gap-3 text-white/85">
                            <svg class="mt-1 size-4 shrink-0 text-white" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M16.7 5.3a1 1 0 0 1 0 1.4l-7.5 7.5a1 1 0 0 1-1.4 0L3.3 9.7a1 1 0 1 1 1.4-1.4l3.8 3.8 6.8-6.8a1 1 0 0 1 1.4 0Z" clip-rule="evenodd"/>
                            </svg>
                            <span class="leading-relaxed">{{ $punto }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="text-ink">
                <x-quote-form heading="Cotiza tu auto"
                              lede="Tres pasos, un par de minutos." />
            </div>
        </div>
    </section>

    {{-- Preguntas. En dos columnas como la sección de la hoja: un bloque de 72
         caracteres suelto dentro de un contenedor de 1240 px dejaba media
         sección vacía y rompía la alineación con el resto de la portada. --}}
    <section class="mx-auto max-w-[1240px] px-5 py-[clamp(4rem,8vw,7rem)]">
        <div class="grid gap-10 lg:grid-cols-[0.62fr_1fr] lg:gap-16">
            <div>
                <h2 class="title-display text-2xl">Preguntas frecuentes</h2>
                <p class="mt-4 max-w-[42ch] leading-relaxed text-ink-muted">
                    Si lo que buscas no está acá, escríbenos por WhatsApp y te respondemos
                    durante el día.
                </p>
                <x-btn href="{{ route('contact.create') }}" variant="outline" class="mt-6 px-5 py-3">
                    Ir a contacto
                </x-btn>
            </div>

            <div class="divide-y divide-border border-y border-border">
                @foreach ([
                ['¿Por qué publican los defectos de los autos?', 'Porque igual los vas a encontrar. Publicarlos antes te ahorra el viaje y a nosotros la conversación incómoda. El precio de cada auto ya considera lo que tiene.'],
                ['¿Puedo ver el auto antes de comprarlo?', 'Sí. Coordinamos y llevamos el auto donde estés para que lo revises y lo manejes. También puedes llevarlo a tu mecánico de confianza.'],
                ['¿Los autos tienen informe legal?', 'Sí. Cada ficha incluye el estado de los papeles y el número de dueños anteriores. El informe completo te lo entregamos antes de cerrar.'],
                ['¿Reciben mi auto en parte de pago?', 'Sí. Escríbenos con los datos de tu auto y lo tasamos para descontarlo del que quieras comprar.'],
                ['¿Cómo se reserva un auto?', 'Escríbenos por WhatsApp desde la ficha del auto que te interesa. Lo marcamos como reservado mientras coordinamos.'],
            ] as [$question, $answer])
                <details class="group py-4">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-4 font-medium">
                        {{ $question }}
                        <svg class="size-5 shrink-0 text-ink-muted transition-transform duration-150 ease-out-quint group-open:rotate-45"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                            <path d="M12 5v14M5 12h14" stroke-linecap="round"/>
                        </svg>
                    </summary>
                    {{-- La animación corre sola al abrir: el contenido pasa de
                         display:none a visible y el navegador la dispara. --}}
                    <p class="anim-fila mt-3 max-w-[68ch] leading-relaxed text-ink-muted">{{ $answer }}</p>
                </details>
                @endforeach
            </div>
        </div>
    </section>
@endsection
