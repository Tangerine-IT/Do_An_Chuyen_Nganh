@props(['light' => false])

<span {{ $attributes->class('inline-flex items-center gap-2.5') }}>
    <span @class([
        'flex h-10 w-10 items-center justify-center rounded-xl shadow-soft',
        'bg-primary-700 text-white' => ! $light,
        'bg-accent-500 text-primary-950' => $light,
    ])>
        <x-icon name="paw" class="h-6 w-6" />
    </span>
    <span @class([
        'font-heading text-2xl font-extrabold tracking-tight',
        'text-primary-900' => ! $light,
        'text-white' => $light,
    ])>{{ config('petshop.name') }}</span>
</span>
