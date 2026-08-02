@extends('layouts.app')

@section('title', 'Privacidad · Pluss Autos')
@section('description', 'Cómo tratamos los datos personales que nos entregas a través del sitio.')

@section('content')
    <div class="mx-auto max-w-[68ch] px-5 py-12">
        <h1 class="title-display text-2xl">Privacidad</h1>
        <p class="figure-mono mt-3 text-sm text-ink-muted">Actualizado en {{ now()->translatedFormat('F \d\e Y') }}</p>

        <div class="mt-10 space-y-8 leading-relaxed">
            <section>
                <h2 class="text-lg font-semibold">Qué datos pedimos</h2>
                <p class="mt-2 text-ink-muted">
                    En los formularios del sitio pedimos tu nombre y teléfono, y de forma
                    opcional tu correo. En el formulario de venta pedimos además los datos del
                    vehículo que quieres vender: marca, modelo, año, kilometraje y, si quieres,
                    la patente.
                </p>
            </section>

            <section>
                <h2 class="text-lg font-semibold">Para qué los usamos</h2>
                <p class="mt-2 text-ink-muted">
                    Únicamente para responder tu consulta o preparar tu cotización. No vendemos
                    ni cedemos tus datos a terceros con fines comerciales, y no te inscribimos
                    en listas de correo sin que lo pidas.
                </p>
            </section>

            <section>
                <h2 class="text-lg font-semibold">Por cuánto tiempo</h2>
                <p class="mt-2 text-ink-muted">
                    Conservamos los datos mientras sean necesarios para la gestión comercial
                    asociada y para cumplir las obligaciones legales que correspondan.
                </p>
            </section>

            <section>
                <h2 class="text-lg font-semibold">Tus derechos</h2>
                <p class="mt-2 text-ink-muted">
                    Puedes pedirnos en cualquier momento que te informemos qué datos tuyos
                    tenemos, que los corrijamos o que los eliminemos. Escríbenos por los
                    <a class="underline underline-offset-2" href="{{ route('contact.create') }}">canales de contacto</a>
                    y lo gestionamos.
                </p>
            </section>

            <section>
                <h2 class="text-lg font-semibold">Cookies</h2>
                <p class="mt-2 text-ink-muted">
                    El sitio usa únicamente las cookies necesarias para su funcionamiento, como
                    la que mantiene tu sesión al enviar un formulario. No usamos cookies de
                    publicidad ni de seguimiento entre sitios.
                </p>
            </section>
        </div>
    </div>
@endsection
