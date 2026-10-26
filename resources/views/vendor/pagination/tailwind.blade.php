@php
    $pageClasses = 'inline-flex h-10 min-w-10 items-center justify-center rounded-full px-3 text-sm font-semibold transition';
@endphp

@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('ui.pagination.label') }}" class="flex flex-col items-center gap-4 sm:flex-row sm:justify-between">
        <p class="text-sm text-ink-muted">
            {{ __('ui.pagination.summary', ['first' => $paginator->firstItem(), 'last' => $paginator->lastItem(), 'total' => $paginator->total()]) }}
        </p>

        <ul class="flex flex-wrap items-center justify-center gap-1.5">
            {{-- Previous page --}}
            <li>
                @if ($paginator->onFirstPage())
                    <span class="{{ $pageClasses }} cursor-not-allowed text-ink-muted opacity-50" aria-disabled="true">
                        <x-icon name="chevron-left" class="h-5 w-5" />
                        <span class="sr-only">{{ __('ui.pagination.previous') }}</span>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="{{ $pageClasses }} bg-white text-primary-800 ring-1 ring-inset ring-line hover:bg-primary-50">
                        <x-icon name="chevron-left" class="h-5 w-5" />
                        <span class="sr-only">{{ __('ui.pagination.previous') }}</span>
                    </a>
                @endif
            </li>

            {{-- Page numbers --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li><span class="px-1 text-ink-muted" aria-hidden="true">{{ $element }}</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li>
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" aria-label="{{ __('ui.pagination.current_page', ['page' => $page]) }}" class="{{ $pageClasses }} bg-primary-700 text-white shadow-soft">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" aria-label="{{ __('ui.pagination.go_to_page', ['page' => $page]) }}" class="{{ $pageClasses }} bg-white text-primary-800 ring-1 ring-inset ring-line hover:bg-primary-50">{{ $page }}</a>
                            @endif
                        </li>
                    @endforeach
                @endif
            @endforeach

            {{-- Next page --}}
            <li>
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="{{ $pageClasses }} bg-white text-primary-800 ring-1 ring-inset ring-line hover:bg-primary-50">
                        <x-icon name="chevron-right" class="h-5 w-5" />
                        <span class="sr-only">{{ __('ui.pagination.next') }}</span>
                    </a>
                @else
                    <span class="{{ $pageClasses }} cursor-not-allowed text-ink-muted opacity-50" aria-disabled="true">
                        <x-icon name="chevron-right" class="h-5 w-5" />
                        <span class="sr-only">{{ __('ui.pagination.next') }}</span>
                    </span>
                @endif
            </li>
        </ul>
    </nav>
@endif
