{{-- Kept for the Breeze profile pages; new code should use <x-button variant="danger">. --}}
<x-button variant="danger" {{ $attributes->merge(['type' => 'submit']) }}>{{ $slot }}</x-button>
