@props(['status'])

@if ($status)
    <div {{ $attributes->class('flex items-start gap-2 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-900 ring-1 ring-emerald-200') }} role="status">
        <x-icon name="check-circle" class="mt-0.5 h-5 w-5 shrink-0" />
        <span>{{ $status }}</span>
    </div>
@endif
