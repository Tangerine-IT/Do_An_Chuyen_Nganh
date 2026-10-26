{{-- Kept for the Breeze profile pages; new code should use <x-button>. --}}
<x-button {{ $attributes->merge(['type' => 'submit']) }}>{{ $slot }}</x-button>
