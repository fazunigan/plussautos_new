@props([
    'label' => 'Fotos en preparación',
    'note' => null,
    'compact' => false,
])

{{--
    Un auto sin fotos es un caso real: el administrador publica el vehículo y
    carga las imágenes después. En vez de un hueco gris con la palabra "sin
    fotos", el espacio queda ocupado por la marca sobre una malla diagonal
    tenue, de modo que el catálogo se vea intencional aunque esté incompleto.
--}}
<div {{ $attributes->class([
    'relative flex flex-col items-center justify-center overflow-hidden bg-surface text-center',
]) }}>
    <div class="pointer-events-none absolute inset-0 opacity-[0.55]"
         style="background-image:
                    linear-gradient(135deg,
                        var(--color-primary-soft) 25%, transparent 25%,
                        transparent 50%, var(--color-primary-soft) 50%,
                        var(--color-primary-soft) 75%, transparent 75%, transparent);
                background-size: 14px 14px;"
         aria-hidden="true"></div>

    <img src="{{ asset('img/mark.webp') }}"
         alt=""
         width="240" height="192"
         loading="lazy" decoding="async"
         aria-hidden="true"
         @class([
             'relative opacity-25',
             'w-10' => $compact,
             'w-16 sm:w-20' => ! $compact,
         ])>

    @unless ($compact)
        <p class="relative mt-3 px-4 text-sm font-medium text-ink-muted">{{ $label }}</p>
        @if ($note)
            <p class="relative mt-1 max-w-[32ch] px-4 text-xs text-ink-muted/80">{{ $note }}</p>
        @endif
    @else
        <span class="sr-only">{{ $label }}</span>
    @endunless
</div>
