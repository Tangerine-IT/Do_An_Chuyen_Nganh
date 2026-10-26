@props(['species'])

@php
    // Works with PetSpecies, ProductSpecies or a plain string value.
    $value = $species instanceof \BackedEnum ? $species->value : (string) $species;

    [$emoji, $tone] = match ($value) {
        'dog' => ['🐶', 'bg-amber-100 text-amber-900'],
        'cat' => ['🐱', 'bg-orange-100 text-orange-900'],
        'bird' => ['🦜', 'bg-emerald-100 text-emerald-900'],
        'fish' => ['🐟', 'bg-sky-100 text-sky-900'],
        'small_pet' => ['🐹', 'bg-rose-100 text-rose-900'],
        default => ['🐾', 'bg-cream-200 text-primary-900'],
    };
@endphp

<span {{ $attributes->class('inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-2.5 py-1 text-xs font-semibold '.$tone) }}>
    <span aria-hidden="true">{{ $emoji }}</span>
    {{ __('enums.product_species.'.$value) }}
</span>
