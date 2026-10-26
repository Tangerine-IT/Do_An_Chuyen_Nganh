<?php

namespace Database\Seeders\Support;

use Illuminate\Support\Facades\Storage;

/**
 * Generates small SVG placeholder images on the public disk so demo data has
 * pictures that work offline (no hotlinking). Requires `php artisan storage:link`
 * for the files to be served from /storage/....
 */
class PlaceholderImage
{
    /**
     * Writes an SVG and returns its path relative to the public disk.
     */
    public static function make(string $path, string $emoji, string $label, string $background = '#FBEFD9'): string
    {
        $label = htmlspecialchars($label, ENT_XML1 | ENT_QUOTES, 'UTF-8');

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 800" role="img" aria-label="{$label}">
  <rect width="800" height="800" fill="{$background}"/>
  <circle cx="400" cy="360" r="210" fill="#FFFFFF" fill-opacity="0.6"/>
  <text x="400" y="440" font-size="230" text-anchor="middle">{$emoji}</text>
  <text x="400" y="680" font-size="42" font-family="Nunito, Quicksand, Arial, sans-serif" font-weight="700" text-anchor="middle" fill="#14532D">{$label}</text>
</svg>
SVG;

        Storage::disk('public')->put($path, $svg);

        return $path;
    }
}
