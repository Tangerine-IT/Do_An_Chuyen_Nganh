<?php

use Illuminate\Support\Facades\Storage;

if (! function_exists('storage_url')) {
    /**
     * Public URL of a file on the public disk (requires `php artisan storage:link`).
     * Returns null when there is no path so views can render a fallback.
     */
    function storage_url(?string $path): ?string
    {
        return $path === null || $path === '' ? null : Storage::disk('public')->url($path);
    }
}

if (! function_exists('format_vnd')) {
    /**
     * Format a whole VND amount for display, e.g. 12500000 => "12.500.000 ₫".
     */
    function format_vnd(int $amount): string
    {
        return number_format($amount, 0, ',', '.').' '.config('petshop.currency_symbol', '₫');
    }
}
