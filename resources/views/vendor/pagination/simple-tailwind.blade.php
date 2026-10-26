@php
    $pageClasses = 'inline-flex items-center gap-1.5 rounded-full px-5 py-2.5 text-sm font-semibold transition';
@endphp

@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('ui.pagination.label') }}" class="flex items-center justify-between gap-3">
        @if ($paginator->onFirstPage())
            <span class="{{ $pageClasses }} cursor-not-allowed text-ink-muted opacity-50" aria-disabled="true">
                <x-icon name="chevron-left" class="h-4 w-4" />
                {{ __('ui.pagination.previous') }}
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="{{ $pageClasses }} bg-white text-primary-800 ring-1 ring-inset ring-line hover:bg-primary-50">
                <x-icon name="chevron-left" class="h-4 w-4" />
                {{ __('ui.pagination.previous') }}
            </a>
        @endif

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="{{ $pageClasses }} bg-white text-primary-800 ring-1 ring-inset ring-line hover:bg-primary-50">
                {{ __('ui.pagination.next') }}
                <x-icon name="chevron-right" class="h-4 w-4" />
            </a>
        @else
            <span class="{{ $pageClasses }} cursor-not-allowed text-ink-muted opacity-50" aria-disabled="true">
                {{ __('ui.pagination.next') }}
                <x-icon name="chevron-right" class="h-4 w-4" />
            </span>
        @endif
    </nav>
@endif
