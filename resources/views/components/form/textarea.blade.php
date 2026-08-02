@props([
    'name',
    'label',
    'rows' => 3,
    'optional' => false,
    'id' => null,
])

@php
    $fieldId = $id ?? 'f-'.$name;
@endphp

<div>
    <label for="{{ $fieldId }}" class="block text-sm font-medium">
        {{ $label }}
        @if ($optional)
            <span class="font-normal text-ink-muted">(opcional)</span>
        @endif
    </label>

    <textarea id="{{ $fieldId }}"
              name="{{ $name }}"
              rows="{{ $rows }}"
              @if ($errors->has($name)) aria-invalid="true" @endif
              {{ $attributes->class([
                  'mt-1.5 w-full rounded-[8px] border px-3 py-2.5',
                  'border-danger' => $errors->has($name),
                  'border-border' => ! $errors->has($name),
              ]) }}>{{ old($name) }}</textarea>

    @error($name)
        <p class="mt-1 text-sm text-danger">{{ $message }}</p>
    @enderror
</div>
