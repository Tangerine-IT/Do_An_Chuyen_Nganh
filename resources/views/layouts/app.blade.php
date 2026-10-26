<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('layouts.partials.head', compact('title', 'description', 'image', 'canonical'))
        @stack('head')
    </head>
    <body class="flex min-h-screen flex-col">
        <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[70] focus:rounded-full focus:bg-white focus:px-5 focus:py-2.5 focus:font-semibold focus:text-primary-800 focus:shadow-lift">
            {{ __('ui.skip_to_content') }}
        </a>

        @include('layouts.partials.site-header')

        @isset($header)
            <header class="border-b border-line bg-white">
                <div class="container-page py-6">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <main id="main" class="flex-1">
            {{ $slot }}
        </main>

        @include('layouts.partials.site-footer')

        <x-toast-container />

        <livewire:chat-widget />
        @livewireScriptConfig
        @stack('scripts')
    </body>
</html>
