@props([
    'name',
    'label',
    'type' => 'text',
    'required' => false,
    'hint' => null,
    'placeholder' => null,
    'optional' => false,
    'mono' => false,
    'value' => null,
    'id' => null,
])

@php
    $fieldId = $id ?? 'f-'.$name;
    $hintId = $hint ? $fieldId.'-hint' : null;
    $errorId = $errors->has($name) ? $fieldId.'-error' : null;
    $describedBy = collect([$hintId, $errorId])->filter()->implode(' ');
@endphp

<div>
    <label for="{{ $fieldId }}" class="block text-sm font-medium">
        {{ $label }}
        @if ($optional)
            <span class="font-normal text-ink-muted">(opcional)</span>
        @endif
    </label>

    <input id="{{ $fieldId }}"
           name="{{ $name }}"
           type="{{ $type }}"
           value="{{ old($name, $value) }}"
           @if ($required) required @endif
           @if ($placeholder) placeholder="{{ $placeholder }}" @endif
           @if ($describedBy !== '') aria-describedby="{{ $describedBy }}" @endif
           @if ($errors->has($name)) aria-invalid="true" @endif
           {{ $attributes->class([
               'mt-1.5 w-full rounded-[8px] border px-3 py-2.5 placeholder:text-ink-muted',
               'figure-mono' => $mono,
               'border-danger' => $errors->has($name),
               'border-border' => ! $errors->has($name),
           ]) }}>

    @if ($hint)
        <p id="{{ $hintId }}" class="mt-1 text-xs text-ink-muted">{{ $hint }}</p>
    @endif

    @error($name)
        <p id="{{ $errorId }}" class="mt-1 text-sm text-danger">{{ $message }}</p>
    @enderror
</div>
