@props([
    'variant' => 'primary',
    'href' => null,
    'type' => 'button',
    'block' => false,
])

@php
    // Sobre fondo blanco manda el azul; sobre el azul del héroe la lógica se
    // invierte a blanco con texto azul. Nunca dos colores saturados peleando
    // dentro del mismo botón.
    $variants = [
        'primary' => 'bg-primary text-white hover:-translate-y-0.5',
        'onDark' => 'bg-bg text-primary hover:-translate-y-0.5',
        'onDarkDeep' => 'bg-bg text-primary-deep hover:-translate-y-0.5',
        'outline' => 'border border-border text-ink hover:border-ink-muted',
        'outlineOnDark' => 'border border-white/35 text-white hover:border-white',
    ];

    $classes = collect([
        'inline-block rounded-[10px] px-6 py-3.5 text-center font-semibold',
        // El hundido al presionar da el acuse táctil que en móvil reemplaza
        // al estado hover, que ahí no existe.
        'transition duration-150 ease-out-quint active:scale-[0.98]',
        $variants[$variant] ?? $variants['primary'],
        $block ? 'block w-full' : '',
    ])->filter()->implode(' ');
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->class($classes) }}>{{ $slot }}</button>
@endif
