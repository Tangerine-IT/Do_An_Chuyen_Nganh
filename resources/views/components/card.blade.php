@props([
    'padded' => true,
    'hover' => false,
])

<div {{ $attributes->class([
    'rounded-2xl bg-white shadow-soft ring-1 ring-line',
    'p-6' => $padded,
    'transition duration-200 hover:-translate-y-1 hover:shadow-lift' => $hover,
]) }}>
    {{ $slot }}
</div>
