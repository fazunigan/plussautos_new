@props(['heading' => 'Cotiza tu auto', 'lede' => null])

@php
    use App\Enums\VehicleCondition;

    // Si la validación del servidor rechazó algo, el formulario abre en el paso
    // que contiene el primer error: dejar al usuario en el paso 1 buscando un
    // mensaje que está tres pasos más adelante es la peor versión de esto.
    $camposPorPaso = [
        1 => ['t_brand', 't_model', 't_version', 't_year'],
        2 => ['t_mileage_km', 't_condition', 't_comuna'],
        3 => ['name', 'phone', 'email', 't_plate', 'message'],
    ];

    $pasoInicial = 1;
    foreach ($camposPorPaso as $numero => $campos) {
        if ($errors->hasAny($campos)) {
            $pasoInicial = $numero;
            break;
        }
    }
@endphp

<div x-data="cotizador({{ $pasoInicial }})" x-ref="tope" class="rounded-[16px] border border-border bg-bg p-6 sm:p-7">
    <div class="flex items-baseline justify-between gap-4">
        <h2 class="text-lg font-semibold">{{ $heading }}</h2>
        <p class="figure-mono shrink-0 text-sm text-ink-muted">
            Paso <span x-text="paso">{{ $pasoInicial }}</span>/3
        </p>
    </div>

    @if ($lede)
        <p class="mt-1.5 text-sm text-ink-muted">{{ $lede }}</p>
    @endif

    {{-- Barra de avance. El texto del aria la hace legible sin ver el color. --}}
    <div class="mt-4 h-1.5 overflow-hidden rounded-full bg-primary-soft"
         role="progressbar"
         :aria-valuenow="paso" aria-valuemin="1" aria-valuemax="3"
         :aria-label="'Paso ' + paso + ' de 3'">
        <div class="h-full rounded-full bg-primary transition-[width] duration-300 ease-out-quint"
             :style="'width:' + (paso / 3 * 100) + '%'"></div>
    </div>

    <form method="POST" action="{{ route('sell.store') }}" class="mt-6">
        @csrf
        <p class="hidden" aria-hidden="true">
            <label>No llenar<input type="text" name="website" tabindex="-1" autocomplete="off"></label>
        </p>

        {{-- Paso 1: el auto --}}
        <div x-ref="paso1" x-show="paso === 1" data-paso="1" class="space-y-4">
            <div class="grid gap-4 sm:grid-cols-2">
                <x-form.field name="t_brand" id="cot-brand" label="Marca"
                              placeholder="Toyota" data-req />
                <x-form.field name="t_model" id="cot-model" label="Modelo"
                              placeholder="Corolla" data-req />
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <x-form.field name="t_version" id="cot-version" label="Versión" optional
                              placeholder="XEI 1.8" />
                <x-form.field name="t_year" id="cot-year" label="Año" type="number" mono
                              min="1950" max="{{ date('Y') + 1 }}" data-req />
            </div>
        </div>

        {{-- Paso 2: el estado --}}
        <div x-ref="paso2" x-show="paso === 2" x-cloak data-paso="2" class="space-y-5">
            <div class="grid gap-4 sm:grid-cols-2">
                <x-form.field name="t_mileage_km" id="cot-km" label="Kilometraje" type="number" mono
                              min="0" step="1000" placeholder="85000" data-req />
                <x-form.field name="t_comuna" id="cot-comuna" label="Comuna" optional
                              placeholder="Ñuñoa"
                              hint="Para coordinar la revisión." />
            </div>

            <fieldset>
                <legend class="text-sm font-medium">¿Cómo está el auto?</legend>
                <div class="mt-2 grid gap-2">
                    @foreach (VehicleCondition::cases() as $condition)
                        <label class="flex cursor-pointer items-start gap-3 rounded-[10px] border border-border p-3 transition-colors duration-150 has-[:checked]:border-primary has-[:checked]:bg-primary-soft">
                            <input type="radio" name="t_condition" value="{{ $condition->value }}"
                                   @checked(old('t_condition') === $condition->value)
                                   data-req
                                   class="mt-1 size-4 accent-[var(--color-primary)]">
                            <span>
                                <span class="block font-medium">{{ $condition->label() }}</span>
                                <span class="block text-sm text-ink-muted">{{ $condition->description() }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
                @error('t_condition') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
            </fieldset>
        </div>

        {{-- Paso 3: el contacto --}}
        <div x-ref="paso3" x-show="paso === 3" x-cloak data-paso="3" class="space-y-4">
            <div class="grid gap-4 sm:grid-cols-2">
                <x-form.field name="name" id="cot-name" label="Nombre" data-req
                              autocomplete="name" />
                <x-form.field name="phone" id="cot-phone" label="Teléfono" type="tel"
                              placeholder="+56 9 1234 5678" data-req
                              autocomplete="tel" />
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <x-form.field name="email" id="cot-email" label="Correo" type="email" optional
                              autocomplete="email" />
                <x-form.field name="t_plate" id="cot-plate" label="Patente" optional mono
                              hint="Con la patente revisamos el historial y la oferta llega más ajustada." />
            </div>
            <x-form.textarea name="message" id="cot-message" label="Algo que debamos saber" optional :rows="3" />
        </div>

        <p x-show="error" x-cloak x-text="error" role="alert"
           class="mt-4 text-sm text-danger"></p>

        <div class="mt-6 flex flex-wrap items-center gap-3">
            <button type="button" x-show="paso > 1" x-cloak @click="anterior()"
                    class="rounded-[10px] border border-border px-5 py-3 font-semibold text-ink transition-colors duration-150 hover:border-ink-muted">
                Atrás
            </button>

            <x-btn type="button" x-show="paso < 3" @click="siguiente()" class="flex-1 sm:flex-none">
                Continuar
            </x-btn>

            <x-btn type="submit" x-show="paso === 3" x-cloak class="flex-1 sm:flex-none">
                Enviar y recibir mi oferta
            </x-btn>
        </div>

        <p class="mt-4 text-xs leading-relaxed text-ink-muted">
            Te respondemos con una oferta concreta dentro del día hábil siguiente.
            Usamos tus datos solo para eso; puedes leer cómo los tratamos en
            <a class="underline underline-offset-2" href="{{ route('privacy') }}">privacidad</a>.
        </p>
    </form>
</div>
