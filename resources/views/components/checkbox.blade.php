@props([
    'name',
    'label',
    'id' => null,
    'value' => '1',
    'checked' => false,
])

@php($id ??= $name)

<label for="{{ $id }}" class="inline-flex cursor-pointer items-center gap-2.5 text-sm text-ink">
    <input
        type="checkbox"
        id="{{ $id }}"
        name="{{ $name }}"
        value="{{ $value }}"
        @checked(old($name, $checked))
        {{ $attributes->class('h-5 w-5 rounded-md border-stroke text-primary-700 focus:ring-primary-600 focus:ring-offset-0') }}
    >
    <span>{{ $label }}</span>
</label>
