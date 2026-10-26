{{-- Kept for the Breeze profile pages; new code should use <x-button variant="secondary">. --}}
<x-button variant="secondary" {{ $attributes->merge(['type' => 'button']) }}>{{ $slot }}</x-button>
