@props(['disabled' => false])

{{-- Bare input kept for the Breeze profile pages; new code should use <x-input>. --}}
<input @disabled($disabled) {{ $attributes->class('form-control') }}>
