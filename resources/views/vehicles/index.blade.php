@extends('layouts.app')

@section('title', 'Autos usados disponibles · Pluss Autos')
@section('description', 'Catálogo de autos usados con inspección publicada. Filtra por marca, año, precio y kilometraje.')

@section('content')
    @php
        /** @var \App\Support\VehicleFilter $filter */
        $chips = [];

        if ($filter->marca) {
            $chips[] = [
                'label' => $brands->firstWhere('slug', $filter->marca)?->name ?? $filter->marca,
                // Quitar la marca también quita el modelo: un modelo sin su marca
                // no filtra nada coherente.
                'url' => request()->fullUrlWithoutQuery(['marca', 'modelo', 'page']),
            ];
        }
        if ($filter->modelo) {
            $chips[] = [
                'label' => $models->firstWhere('slug', $filter->modelo)?->name ?? $filter->modelo,
                'url' => request()->fullUrlWithoutQuery(['modelo', 'page']),
            ];
        }
        if ($filter->anioMin) {
            $chips[] = ['label' => "Desde {$filter->anioMin}", 'url' => request()->fullUrlWithoutQuery(['anio_min', 'page'])];
        }
        if ($filter->anioMax) {
            $chips[] = ['label' => "Hasta {$filter->anioMax}", 'url' => request()->fullUrlWithoutQuery(['anio_max', 'page'])];
        }
        if ($filter->precioMin) {
            $chips[] = ['label' => 'Desde $'.number_format($filter->precioMin, 0, ',', '.'), 'url' => request()->fullUrlWithoutQuery(['precio_min', 'page'])];
        }
        if ($filter->precioMax) {
            $chips[] = ['label' => 'Hasta $'.number_format($filter->precioMax, 0, ',', '.'), 'url' => request()->fullUrlWithoutQuery(['precio_max', 'page'])];
        }
        if ($filter->kmMax) {
            $chips[] = ['label' => 'Hasta '.number_format($filter->kmMax, 0, ',', '.').' km', 'url' => request()->fullUrlWithoutQuery(['km_max', 'page'])];
        }
        if ($filter->transmision) {
            $chips[] = ['label' => $filter->transmision->label(), 'url' => request()->fullUrlWithoutQuery(['transmision', 'page'])];
        }
        if ($filter->combustible) {
            $chips[] = ['label' => $filter->combustible->label(), 'url' => request()->fullUrlWithoutQuery(['combustible', 'page'])];
        }
        if ($filter->carroceria) {
            $chips[] = ['label' => $filter->carroceria->label(), 'url' => request()->fullUrlWithoutQuery(['carroceria', 'page'])];
        }
    @endphp

    <div class="mx-auto max-w-[1240px] px-5 py-10">
        <header>
            <h1 class="title-display text-2xl">Autos disponibles</h1>
            <p class="mt-3 max-w-[62ch] leading-relaxed text-ink-muted">
                Cada ficha trae la inspección completa: qué está conforme, qué tiene observaciones
                y una foto de cada detalle.
            </p>
        </header>

        <div class="mt-9 grid gap-10 lg:grid-cols-[260px_1fr] lg:gap-12">
            {{-- Filtros en escritorio --}}
            <aside class="hidden lg:block">
                <h2 class="sr-only">Filtros</h2>
                <x-catalog-filters :filter="$filter" :brands="$brands" :models="$models"
                                   :transmissions="$transmissions" :fuels="$fuels" :body-types="$bodyTypes" />
            </aside>

            <div x-data="{
                abrir() { $refs.hoja.showModal() },
                cerrar() { $refs.hoja.close() }
            }">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <p class="figure-mono text-sm text-ink-muted">
                        {{ $vehicles->total() }} {{ \Illuminate\Support\Str::plural('auto', $vehicles->total()) }}
                    </p>

                    <div class="flex items-center gap-3">
                        <button type="button" @click="abrir()"
                                class="rounded-[10px] border border-border px-4 py-2.5 text-sm font-semibold lg:hidden">
                            Filtros{{ count($chips) ? ' ('.count($chips).')' : '' }}
                        </button>

                        <form method="GET" action="{{ route('vehicles.index') }}">
                            @foreach ($filter->toQuery() as $key => $value)
                                @if ($key !== 'orden')
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endif
                            @endforeach
                            <label for="orden" class="sr-only">Ordenar por</label>
                            <select id="orden" name="orden" onchange="this.form.submit()"
                                    class="rounded-[10px] border border-border bg-bg px-3 py-2.5 text-sm text-ink">
                                @foreach (\App\Support\VehicleFilter::SORTS as $value => $label)
                                    <option value="{{ $value }}" @selected($filter->orden === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                </div>

                @if ($chips !== [])
                    <ul class="mt-4 flex flex-wrap gap-2">
                        @foreach ($chips as $chip)
                            <li>
                                <a href="{{ $chip['url'] }}"
                                   class="inline-flex items-center gap-1.5 rounded-full bg-primary-soft py-1.5 pl-3 pr-2.5 text-sm text-ink transition-colors duration-150 hover:bg-border">
                                    {{ $chip['label'] }}
                                    <span class="sr-only">Quitar filtro</span>
                                    <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                                        <path d="M6 6l12 12M18 6L6 18" stroke-linecap="round"/>
                                    </svg>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif

                @if ($vehicles->isEmpty())
                    <div class="mt-10 rounded-[10px] border border-border p-10 text-center">
                        <p class="text-lg font-semibold">No hay autos con esos filtros</p>
                        <p class="mx-auto mt-2 max-w-[46ch] text-ink-muted">
                            Prueba ampliando el rango de precio o de año. Si buscas algo puntual,
                            escríbenos y lo buscamos por ti.
                        </p>
                        <a href="{{ route('vehicles.index') }}"
                           class="mt-6 inline-block rounded-[10px] bg-primary px-5 py-3 font-semibold text-white">
                            Ver todos los autos
                        </a>
                    </div>
                @else
                    {{-- El escalonado es la respuesta visible al filtro aplicado:
                         en este catálogo filtrar recarga la página. --}}
                    <div class="anim-grid mt-6 grid gap-6 [grid-template-columns:repeat(auto-fit,minmax(280px,1fr))]">
                        @foreach ($vehicles as $vehicle)
                            <x-vehicle-card :vehicle="$vehicle" style="--i: {{ $loop->index }}" />
                        @endforeach
                    </div>

                    <div class="mt-10">
                        {{ $vehicles->onEachSide(1)->links() }}
                    </div>
                @endif

                {{-- Filtros en móvil: dialog nativo, no un desplegable absoluto
                     que quedaría recortado por el contenedor. --}}
                <dialog x-ref="hoja"
                        class="m-0 mt-auto w-full max-w-none rounded-t-[16px] bg-bg p-0 backdrop:bg-ink/50 lg:hidden">
                    <div class="max-h-[85dvh] overflow-y-auto p-5">
                        <div class="mb-5 flex items-center justify-between">
                            <h2 class="text-lg font-semibold">Filtros</h2>
                            <button type="button" @click="cerrar()"
                                    class="-mr-2 inline-flex size-11 items-center justify-center rounded-[10px]">
                                <span class="sr-only">Cerrar filtros</span>
                                <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                    <path d="M6 6l12 12M18 6L6 18" stroke-linecap="round"/>
                                </svg>
                            </button>
                        </div>

                        <x-catalog-filters :filter="$filter" :brands="$brands" :models="$models"
                                           :transmissions="$transmissions" :fuels="$fuels" :body-types="$bodyTypes" />
                    </div>
                </dialog>
            </div>
        </div>
    </div>
@endsection
