@props(['value'])

<label {{ $attributes->class('mb-1.5 block text-sm font-semibold text-primary-900') }}>
    {{ $value ?? $slot }}
</label>
