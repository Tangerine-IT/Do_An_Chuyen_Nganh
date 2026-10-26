<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('layouts.partials.head', ['title' => $heading])
    </head>
    <body class="min-h-screen">
        <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[70] focus:rounded-full focus:bg-white focus:px-5 focus:py-2.5 focus:font-semibold focus:text-primary-800 focus:shadow-lift">
            {{ __('ui.skip_to_content') }}
        </a>

        <div class="grid min-h-screen lg:grid-cols-2">
            {{-- Brand panel (desktop only) --}}
            <aside class="relative hidden overflow-hidden bg-primary-800 p-12 text-white lg:flex lg:flex-col lg:justify-between">
                <x-icon name="paw" class="pointer-events-none absolute -end-16 -top-10 h-80 w-80 rotate-12 text-primary-700/60" />
                <x-icon name="paw" class="pointer-events-none absolute -bottom-16 -start-10 h-64 w-64 -rotate-12 text-primary-700/60" />

                <a href="{{ route('home') }}" class="relative inline-block self-start rounded-xl" aria-label="{{ config('petshop.name') }}">
                    <x-logo light />
                </a>

                <div class="relative max-w-md space-y-6">
                    <h2 class="text-4xl leading-tight text-white">{{ __('ui.auth.panel_title') }}</h2>
                    <p class="text-lg text-primary-100">{{ __('ui.auth.panel_text') }}</p>
                    <ul class="space-y-3 text-primary-50">
                        @foreach (['panel_point_1', 'panel_point_2', 'panel_point_3'] as $point)
                            <li class="flex items-center gap-3">
                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-accent-500 text-primary-950">
                                    <x-icon name="check" class="h-4 w-4" />
                                </span>
                                {{ __('ui.auth.'.$point) }}
                            </li>
                        @endforeach
                    </ul>
                </div>

                <p class="relative text-sm text-primary-200">&copy; {{ now()->year }} {{ config('petshop.name') }}</p>
            </aside>

            {{-- Form --}}
            <main id="main" class="flex flex-col justify-center px-4 py-10 sm:px-8">
                <div class="mx-auto w-full max-w-md">
                    <a href="{{ route('home') }}" class="mb-8 inline-block rounded-xl lg:hidden" aria-label="{{ config('petshop.name') }}">
                        <x-logo />
                    </a>

                    @if ($heading)
                        <h1 class="text-3xl">{{ $heading }}</h1>
                    @endif
                    @if ($subheading)
                        <p class="mt-2 text-ink-muted">{{ $subheading }}</p>
                    @endif

                    <div class="mt-8">
                        {{ $slot }}
                    </div>
                </div>
            </main>
        </div>

        <x-toast-container />

        @livewireScriptConfig
    </body>
</html>
