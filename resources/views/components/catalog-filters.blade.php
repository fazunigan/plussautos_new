@props(['filter', 'brands', 'models', 'transmissions', 'fuels', 'bodyTypes'])

@php
    /** @var \App\Support\VehicleFilter $filter */
@endphp

<form method="GET" action="{{ route('vehicles.index') }}" class="space-y-6">
    {{-- El orden viaja con el formulario para no perderse al filtrar. --}}
    <input type="hidden" name="orden" value="{{ $filter->orden }}">

    <div>
        <label for="f-marca" class="block text-sm font-semibold">Marca</label>
        <select id="f-marca" name="marca"
                onchange="this.form.querySelector('#f-modelo').value = ''; this.form.submit()"
                class="mt-2 w-full rounded-[8px] border border-border bg-bg px-3 py-2.5 text-ink">
            <option value="">Todas las marcas</option>
            @foreach ($brands as $brand)
                <option value="{{ $brand->slug }}" @selected($filter->marca === $brand->slug)>
                    {{ $brand->name }}
                </option>
            @endforeach
        </select>
    </div>

    @if ($models->isNotEmpty())
        <div>
            <label for="f-modelo" class="block text-sm font-semibold">Modelo</label>
            <select id="f-modelo" name="modelo"
                    class="mt-2 w-full rounded-[8px] border border-border bg-bg px-3 py-2.5 text-ink">
                <option value="">Todos los modelos</option>
                @foreach ($models as $model)
                    <option value="{{ $model->slug }}" @selected($filter->modelo === $model->slug)>
                        {{ $model->name }}
                    </option>
                @endforeach
            </select>
        </div>
    @else
        <input type="hidden" id="f-modelo" name="modelo" value="">
    @endif

    <fieldset>
        <legend class="text-sm font-semibold">Año</legend>
        <div class="mt-2 grid grid-cols-2 gap-2">
            <input type="number" name="anio_min" value="{{ $filter->anioMin }}"
                   min="1950" max="{{ date('Y') + 1 }}" placeholder="Desde"
                   aria-label="Año desde"
                   class="figure-mono w-full rounded-[8px] border border-border bg-bg px-3 py-2.5 text-ink placeholder:text-ink-muted">
            <input type="number" name="anio_max" value="{{ $filter->anioMax }}"
                   min="1950" max="{{ date('Y') + 1 }}" placeholder="Hasta"
                   aria-label="Año hasta"
                   class="figure-mono w-full rounded-[8px] border border-border bg-bg px-3 py-2.5 text-ink placeholder:text-ink-muted">
        </div>
    </fieldset>

    <fieldset>
        <legend class="text-sm font-semibold">Precio</legend>
        <div class="mt-2 grid grid-cols-2 gap-2">
            <input type="number" name="precio_min" value="{{ $filter->precioMin }}"
                   min="0" step="500000" placeholder="Desde"
                   aria-label="Precio desde"
                   class="figure-mono w-full rounded-[8px] border border-border bg-bg px-3 py-2.5 text-ink placeholder:text-ink-muted">
            <input type="number" name="precio_max" value="{{ $filter->precioMax }}"
                   min="0" step="500000" placeholder="Hasta"
                   aria-label="Precio hasta"
                   class="figure-mono w-full rounded-[8px] border border-border bg-bg px-3 py-2.5 text-ink placeholder:text-ink-muted">
        </div>
    </fieldset>

    <div>
        <label for="f-km" class="block text-sm font-semibold">Kilometraje máximo</label>
        <input id="f-km" type="number" name="km_max" value="{{ $filter->kmMax }}"
               min="0" step="10000" placeholder="Sin límite"
               class="figure-mono mt-2 w-full rounded-[8px] border border-border bg-bg px-3 py-2.5 text-ink placeholder:text-ink-muted">
    </div>

    <div>
        <label for="f-transmision" class="block text-sm font-semibold">Transmisión</label>
        <select id="f-transmision" name="transmision"
                class="mt-2 w-full rounded-[8px] border border-border bg-bg px-3 py-2.5 text-ink">
            <option value="">Cualquiera</option>
            @foreach ($transmissions as $value => $label)
                <option value="{{ $value }}" @selected($filter->transmision?->value === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="f-combustible" class="block text-sm font-semibold">Combustible</label>
        <select id="f-combustible" name="combustible"
                class="mt-2 w-full rounded-[8px] border border-border bg-bg px-3 py-2.5 text-ink">
            <option value="">Cualquiera</option>
            @foreach ($fuels as $value => $label)
                <option value="{{ $value }}" @selected($filter->combustible?->value === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="f-carroceria" class="block text-sm font-semibold">Carrocería</label>
        <select id="f-carroceria" name="carroceria"
                class="mt-2 w-full rounded-[8px] border border-border bg-bg px-3 py-2.5 text-ink">
            <option value="">Cualquiera</option>
            @foreach ($bodyTypes as $value => $label)
                <option value="{{ $value }}" @selected($filter->carroceria?->value === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div class="flex gap-3">
        <button type="submit"
                class="flex-1 rounded-[10px] bg-primary px-4 py-3 font-semibold text-white transition-transform duration-150 ease-out-quint hover:-translate-y-0.5">
            Aplicar filtros
        </button>
        @unless ($filter->isEmpty())
            <a href="{{ route('vehicles.index') }}"
               class="rounded-[10px] border border-border px-4 py-3 font-semibold text-ink">
                Limpiar
            </a>
        @endunless
    </div>
</form>
