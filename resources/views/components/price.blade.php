@props([
    'product' => null,
    'price' => null,
    'salePrice' => null,
    'size' => 'md',
])

@php
    // Accepts either a Product, or explicit price/salePrice values (whole VND).
    if ($product) {
        $price = $product->price;
        $salePrice = $product->is_on_sale ? $product->sale_price : null;
    }

    $onSale = $price !== null && $salePrice !== null && $salePrice < $price;

    $main = match ($size) {
        'sm' => 'text-base',
        'lg' => 'text-3xl',
        default => 'text-xl',
    };
@endphp

@if ($price !== null)
    <div {{ $attributes->class('flex flex-wrap items-baseline gap-x-2 gap-y-0.5') }}>
        @if ($onSale)
            <span class="sr-only">{{ __('ui.price.sale') }}:</span>
            <span class="font-heading font-extrabold text-accent-700 {{ $main }}">{{ format_vnd($salePrice) }}</span>
            <span class="sr-only">{{ __('ui.price.original') }}:</span>
            <del class="text-sm text-ink-muted">{{ format_vnd($price) }}</del>
        @else
            <span class="font-heading font-extrabold text-primary-800 {{ $main }}">{{ format_vnd($price) }}</span>
        @endif
    </div>
@endif
