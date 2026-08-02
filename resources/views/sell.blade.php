@extends('layouts.app')

@section('title', 'Cotiza tu auto online en minutos · Pluss Autos')
@section('description', 'Cuéntanos qué auto tienes y te respondemos con una oferta concreta dentro del día hábil siguiente. Sin publicar tu teléfono ni recibir desconocidos en tu casa.')

@section('content')
    <div class="mx-auto max-w-[1120px] px-5 py-12">
        <div class="grid gap-12 lg:grid-cols-[1fr_480px] lg:gap-16">
            <div>
                <h1 class="title-display text-2xl">Cotiza tu auto online en minutos</h1>
                <p class="mt-5 max-w-[62ch] text-lg leading-relaxed text-ink-muted">
                    Publicar en un clasificado significa dar tu teléfono a cualquiera y recibir
                    desconocidos en tu casa. Acá no: llenas el formulario, lo revisamos y te
                    respondemos con una oferta concreta.
                </p>

                <ol class="mt-10 space-y-6">
                    @foreach ([
                        ['Completas el formulario', 'Marca, modelo, año, kilometraje y en qué estado está. Tres pasos, un par de minutos.'],
                        ['Te damos un rango de precio', 'Te respondemos dentro del día hábil siguiente con un rango realista, no con una cifra inflada para engancharte.'],
                        ['Revisamos el auto', 'Coordinamos la revisión donde a ti te acomode. De ahí sale la oferta final.'],
                        ['Cerramos y te pagamos', 'Transferencia digital y pago inmediato. También podemos tomarlo en consignación si prefieres.'],
                    ] as $index => [$title, $body])
                        <li class="flex gap-5">
                            <span class="figure-mono shrink-0 text-sm text-primary">0{{ $index + 1 }}</span>
                            <div>
                                <h2 class="font-semibold">{{ $title }}</h2>
                                <p class="mt-1 max-w-[58ch] leading-relaxed text-ink-muted">{{ $body }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>

                <div class="mt-10 rounded-[16px] bg-surface p-6">
                    <h2 class="font-semibold">¿Por qué preguntamos en qué estado está?</h2>
                    <p class="mt-2 max-w-[58ch] leading-relaxed text-ink-muted">
                        Porque de eso depende la oferta. Un auto con detalles no vale menos por
                        castigo: vale lo que vale una vez descontado lo que hay que repararle.
                        Si nos lo dices desde el principio, la cifra que te damos es la que
                        terminas recibiendo.
                    </p>
                </div>
            </div>

            <div class="lg:sticky lg:top-24 lg:self-start">
                <x-quote-form heading="Cotiza tu auto"
                              lede="Tres pasos. Te respondemos con una oferta concreta." />
            </div>
        </div>
    </div>
@endsection
