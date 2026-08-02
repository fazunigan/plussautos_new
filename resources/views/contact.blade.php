@extends('layouts.app')

@section('title', 'Contacto · Pluss Autos')
@section('description', 'Escríbenos por WhatsApp o déjanos tus datos y te respondemos a la brevedad.')

@section('content')
    @php $settings = \App\Models\SiteSetting::current(); @endphp

    <div class="mx-auto max-w-[1120px] px-5 py-12">
        <div class="grid gap-12 lg:grid-cols-2 lg:gap-16">
            <div>
                <h1 class="title-display text-2xl">Hablemos</h1>
                <p class="mt-5 max-w-[58ch] text-lg leading-relaxed text-ink-muted">
                    La vía más rápida es WhatsApp: ahí respondemos durante el día.
                    Si prefieres, déjanos tus datos y te llamamos.
                </p>

                <dl class="mt-10 divide-y divide-border border-y border-border">
                    <div class="flex items-baseline justify-between gap-6 py-4">
                        <dt class="text-ink-muted">WhatsApp</dt>
                        <dd>
                            <a href="{{ $settings->whatsappUrl('Hola, quiero información.') }}"
                               class="figure-mono font-medium underline underline-offset-4">
                                {{ $settings->whatsapp }}
                            </a>
                        </dd>
                    </div>

                    @if ($settings->email)
                        <div class="flex items-baseline justify-between gap-6 py-4">
                            <dt class="text-ink-muted">Correo</dt>
                            <dd><a href="mailto:{{ $settings->email }}" class="font-medium underline underline-offset-4">{{ $settings->email }}</a></dd>
                        </div>
                    @endif

                    {{-- Dirección y horario se muestran solo si están cargados en el
                         panel. Vacíos, la fila no existe. --}}
                    @if ($settings->address)
                        <div class="flex items-baseline justify-between gap-6 py-4">
                            <dt class="text-ink-muted">Dirección</dt>
                            <dd class="max-w-[30ch] text-right">{{ $settings->address }}</dd>
                        </div>
                    @endif

                    @if ($settings->hours)
                        <div class="flex items-baseline justify-between gap-6 py-4">
                            <dt class="text-ink-muted">Horario</dt>
                            <dd class="max-w-[30ch] text-right">{{ $settings->hours }}</dd>
                        </div>
                    @endif

                    @if ($settings->instagram)
                        <div class="flex items-baseline justify-between gap-6 py-4">
                            <dt class="text-ink-muted">Instagram</dt>
                            <dd><a href="{{ $settings->instagram }}" class="font-medium underline underline-offset-4">Ver perfil</a></dd>
                        </div>
                    @endif
                </dl>

                <div class="mt-10 rounded-[16px] bg-surface p-6">
                    <h2 class="font-semibold">¿Quieres vender tu auto?</h2>
                    <p class="mt-2 max-w-[52ch] leading-relaxed text-ink-muted">
                        El cotizador toma un par de minutos y te respondemos con una oferta
                        concreta dentro del día hábil siguiente.
                    </p>
                    <x-btn href="{{ route('sell.create') }}" class="mt-5 px-5 py-3">
                        Cotizar mi auto
                    </x-btn>
                </div>
            </div>

            <div class="rounded-[16px] border border-border p-6 lg:sticky lg:top-24 lg:self-start">
                <h2 class="text-lg font-semibold">Déjanos tus datos</h2>

                <form method="POST" action="{{ route('contact.store') }}" class="mt-6 space-y-4">
                    @csrf
                    <p class="hidden" aria-hidden="true">
                        <label>No llenar<input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                    </p>

                    <x-form.field name="name" id="k-name" label="Nombre" required autocomplete="name" />
                    <x-form.field name="phone" id="k-phone" label="Teléfono" type="tel" required
                                  placeholder="+56 9 1234 5678" autocomplete="tel" />
                    <x-form.field name="email" id="k-email" label="Correo" type="email" optional
                                  autocomplete="email" />
                    <x-form.textarea name="message" id="k-message" label="Mensaje" :rows="4" />

                    <x-btn type="submit" block class="px-4 py-3">Enviar mensaje</x-btn>
                </form>
            </div>
        </div>
    </div>
@endsection
