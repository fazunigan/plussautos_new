@props(['brands', 'availableCount' => 0])

@php
    use App\Enums\BodyType;

    // Tramos pensados para el mercado chileno de usados. Son rangos redondos
    // porque el comprador piensa en "hasta 10 millones", no en 9.750.000.
    $tramos = [
        6_000_000 => 'Hasta $6.000.000',
        10_000_000 => 'Hasta $10.000.000',
        15_000_000 => 'Hasta $15.000.000',
        20_000_000 => 'Hasta $20.000.000',
        30_000_000 => 'Hasta $30.000.000',
    ];
@endphp

<div class="rounded-[16px] bg-bg p-6 text-ink shadow-[0_8px_30px_oklch(0.2_0.022_259_/_0.18)]">
    <h2 class="text-lg font-semibold">Busca tu auto</h2>
    <p class="mt-1 text-sm text-ink-muted">
        <span class="figure-mono">{{ $availableCount }}</span>
        {{ \Illuminate\Support\Str::plural('auto', $availableCount) }}
        {{ \Illuminate\Support\Str::plural('disponible', $availableCount) }} ahora.
    </p>

    <form method="GET" action="{{ route('vehicles.index') }}" class="mt-5 space-y-3">
        <div>
            <label for="qs-marca" class="block text-sm font-medium">Marca</label>
            <select id="qs-marca" name="marca"
                    class="mt-1.5 w-full rounded-[8px] border border-border bg-bg px-3 py-2.5">
                <option value="">Todas las marcas</option>
                @foreach ($brands as $brand)
                    <option value="{{ $brand->slug }}">{{ $brand->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid gap-3 sm:grid-cols-2">
            <div>
                <label for="qs-carroceria" class="block text-sm font-medium">Tipo</label>
                <select id="qs-carroceria" name="carroceria"
                        class="mt-1.5 w-full rounded-[8px] border border-border bg-bg px-3 py-2.5">
                    <option value="">Cualquiera</option>
                    @foreach (BodyType::options() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="qs-precio" class="block text-sm font-medium">Presupuesto</label>
                <select id="qs-precio" name="precio_max"
                        class="mt-1.5 w-full rounded-[8px] border border-border bg-bg px-3 py-2.5">
                    <option value="">Sin límite</option>
                    @foreach ($tramos as $valor => $label)
                        <option value="{{ $valor }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <x-btn type="submit" block class="px-4 py-3">Ver autos</x-btn>
    </form>
</div>
