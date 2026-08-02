@extends('layouts.app')

@section('title', 'Términos y condiciones · Pluss Autos')
@section('description', 'Términos y condiciones de uso del sitio y de las operaciones de compra y venta de vehículos.')

@section('content')
    <div class="mx-auto max-w-[68ch] px-5 py-12">
        <h1 class="title-display text-2xl">Términos y condiciones</h1>
        <p class="figure-mono mt-3 text-sm text-ink-muted">Actualizado en {{ now()->translatedFormat('F \d\e Y') }}</p>

        <div class="mt-10 space-y-8 leading-relaxed">
            <section>
                <h2 class="text-lg font-semibold">Sobre este sitio</h2>
                <p class="mt-2 text-ink-muted">
                    Pluss Autos publica vehículos usados en venta, propios y en consignación.
                    La información de cada ficha se prepara con la mayor precisión posible al
                    momento de publicarla.
                </p>
            </section>

            <section>
                <h2 class="text-lg font-semibold">Precios y disponibilidad</h2>
                <p class="mt-2 text-ink-muted">
                    Los precios están expresados en pesos chilenos e incluyen los impuestos que
                    correspondan. Un vehículo puede venderse o reservarse en cualquier momento,
                    de modo que la publicación no constituye una oferta irrevocable.
                </p>
            </section>

            <section>
                <h2 class="text-lg font-semibold">Estado de los vehículos</h2>
                <p class="mt-2 text-ink-muted">
                    La hoja de inspección de cada ficha refleja lo observado durante nuestra
                    revisión. No sustituye una revisión mecánica independiente: puedes llevar
                    cualquier vehículo a tu mecánico de confianza antes de comprarlo.
                </p>
            </section>

            <section>
                <h2 class="text-lg font-semibold">Reservas</h2>
                <p class="mt-2 text-ink-muted">
                    Una reserva se coordina directamente por WhatsApp y mantiene el vehículo
                    fuera del catálogo durante el plazo que acordemos. Las condiciones de cada
                    reserva se pactan por escrito en esa conversación.
                </p>
            </section>

            <section>
                <h2 class="text-lg font-semibold">Vehículos en consignación</h2>
                <p class="mt-2 text-ink-muted">
                    Parte del catálogo corresponde a vehículos de terceros que gestionamos con
                    mandato del propietario. En esos casos actuamos como intermediarios y así
                    se indica en la documentación de la operación.
                </p>
            </section>

            <section>
                <h2 class="text-lg font-semibold">Contacto</h2>
                <p class="mt-2 text-ink-muted">
                    Cualquier consulta sobre estos términos puedes hacerla por los
                    <a class="underline underline-offset-2" href="{{ route('contact.create') }}">canales de contacto</a>.
                </p>
            </section>
        </div>
    </div>
@endsection
