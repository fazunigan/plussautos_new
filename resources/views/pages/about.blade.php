@extends('layouts.app')

@section('title', 'Cómo trabajamos · Pluss Autos')
@section('description', 'Revisamos cada auto en ocho categorías y publicamos el resultado completo, con foto de cada observación.')

@section('content')
    <section class="bg-primary text-white">
        <div class="mx-auto max-w-[1120px] px-5 py-[clamp(3rem,7vw,5rem)]">
            <h1 class="title-display max-w-[20ch] text-3xl">
                Cualquiera puede decir que su auto está impecable.
            </h1>
            <p class="mt-6 max-w-[60ch] text-lg leading-relaxed text-white/85">
                Por eso no lo decimos. Publicamos los {{ \App\Support\InspectionChecklist::totalPoints() }} puntos
                que revisamos en cada auto, uno por uno, con lo que encontramos en cada uno.
            </p>
        </div>
    </section>

    <div class="mx-auto max-w-[1120px] px-5 py-[clamp(3rem,7vw,5rem)]">
        <section>
            <h2 class="title-display text-2xl">Por qué publicamos los defectos</h2>
            <div class="mt-5 max-w-[68ch] space-y-4 leading-relaxed text-ink-muted">
                <p>
                    Un auto usado tiene marcas de uso. Todos. El que dice que no las tiene está
                    esperando que no las mires con atención, o que las descubras cuando ya sea tarde.
                </p>
                <p>
                    Nosotros preferimos que las veas antes. Cada observación va con su foto,
                    su ubicación y su descripción, y el precio del auto ya la considera. Si igual
                    quieres verlo tú mismo, te lo llevamos donde estés, y puedes pasarlo por tu
                    mecánico de confianza.
                </p>
                <p>
                    Y para que no tengas que salir a buscarlo, coordinamos la prueba donde a ti
                    te acomode: tu casa, tu trabajo o donde prefieras. Vamos con el auto y con
                    los papeles, y si no te convence, no pasa nada.
                </p>
            </div>
        </section>

        <section class="mt-[clamp(3rem,6vw,4.5rem)]">
            <h2 class="title-display text-2xl">Qué revisamos</h2>
            <p class="mt-3 max-w-[64ch] leading-relaxed text-ink-muted">
                La misma pauta para todos los autos. Es lo que te permite comparar dos fichas
                entre sí sin quedarte con la duda de si a uno lo revisaron con más cuidado.
            </p>

            <div class="mt-8 grid gap-x-12 gap-y-9 sm:grid-cols-2">
                @foreach (\App\Enums\InspectionCategory::cases() as $category)
                    <div>
                        <h3 class="flex items-baseline gap-3 font-semibold">
                            <span class="figure-mono text-sm text-primary">
                                {{ str_pad((string) $category->order(), 2, '0', STR_PAD_LEFT) }}
                            </span>
                            {{ $category->label() }}
                        </h3>
                        <ul class="mt-2.5 space-y-1.5 text-ink-muted">
                            @foreach (\App\Support\InspectionChecklist::pointsFor($category) as $point)
                                <li class="text-sm">{{ $point }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="mt-[clamp(3rem,6vw,4.5rem)] rounded-[16px] bg-surface p-8">
            <h2 class="title-display text-xl">Los tres estados</h2>
            <dl class="mt-6 space-y-5">
                @foreach ([
                    ['Conforme', 'El punto se revisó y está bien. Sin peros.'],
                    ['Con observación', 'Encontramos algo. Va con foto y descripción, y el precio lo considera.'],
                    ['Reparado', 'Tenía un problema y lo arreglamos antes de publicarlo. Te contamos qué era.'],
                ] as [$term, $definition])
                    <div class="border-b border-border pb-5 last:border-0 last:pb-0">
                        <dt class="font-semibold">{{ $term }}</dt>
                        <dd class="mt-1 max-w-[62ch] leading-relaxed text-ink-muted">{{ $definition }}</dd>
                    </div>
                @endforeach
            </dl>
        </section>

        <section class="mt-[clamp(3rem,6vw,4.5rem)] flex flex-wrap gap-3">
            <a href="{{ route('vehicles.index') }}"
               class="rounded-[10px] bg-primary px-6 py-3.5 font-semibold text-white">
                Ver autos disponibles
            </a>
            <a href="{{ route('sell.create') }}"
               class="rounded-[10px] border border-border px-6 py-3.5 font-semibold text-ink">
                Vender mi auto
            </a>
        </section>
    </div>
@endsection
