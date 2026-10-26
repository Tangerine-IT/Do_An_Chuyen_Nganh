@props([
    'label' => null,
    'for' => null,
    'hint' => null,
    'error' => null,
    'required' => false,
])

{{-- Label, hint and error wrapper shared by x-input, x-select and x-textarea. --}}
<div {{ $attributes->class('w-full') }}>
    @if ($label)
        <label for="{{ $for }}" class="mb-1.5 block text-sm font-semibold text-primary-900">
            {{ $label }}@if ($required)<span class="text-accent-700" aria-hidden="true"> *</span>@endif
        </label>
    @endif

    {{ $slot }}

    @if ($error)
        <p id="{{ $for }}-error" class="mt-1.5 flex items-start gap-1.5 text-sm font-medium text-red-800">
            <x-icon name="warning" class="mt-0.5 h-4 w-4 shrink-0" />
            <span>{{ $error }}</span>
        </p>
    @elseif ($hint)
        <p id="{{ $for }}-hint" class="mt-1.5 text-sm text-ink-muted">{{ $hint }}</p>
    @endif
</div>
