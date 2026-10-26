@php
    // Items whose route does not exist yet are shown as disabled; they become links as the admin pages are built.
    $navItems = [
        ['route' => 'admin.dashboard', 'icon' => 'chart', 'label' => __('ui.admin.nav.dashboard')],
        ['route' => 'admin.products.index', 'icon' => 'bag', 'label' => __('ui.admin.nav.products')],
        ['route' => 'admin.categories.index', 'icon' => 'bag', 'label' => 'Danh mục'],
        ['route' => 'admin.brands.index', 'icon' => 'tag', 'label' => 'Thương hiệu'],
        ['route' => 'admin.services.index', 'icon' => 'calendar', 'label' => 'Dịch vụ'],
        ['route' => 'admin.orders.index', 'icon' => 'cart', 'label' => __('ui.admin.nav.orders')],
        ['route' => 'admin.users.index', 'icon' => 'users', 'label' => __('ui.admin.nav.customers')],
        ['route' => 'admin.bookings.index', 'icon' => 'calendar', 'label' => __('ui.admin.nav.bookings')],
        ['route' => 'admin.coupons.index', 'icon' => 'tag', 'label' => __('ui.admin.nav.coupons')],
        ['route' => 'admin.reviews.index', 'icon' => 'star', 'label' => __('ui.admin.nav.reviews')],
        ['route' => 'admin.reports.index', 'icon' => 'chart', 'label' => __('ui.admin.nav.reports')],
        ['route' => 'admin.posts.index', 'icon' => 'tag', 'label' => __('ui.admin.nav.posts')],
        ['route' => 'admin.adoption.listings', 'icon' => 'users', 'label' => __('ui.adoption.admin_listings')],
        ['route' => 'admin.locations.index', 'icon' => 'map-pin', 'label' => __('ui.map.admin_locations')],
    ];

    if (auth()->user()?->isStaff()) {
        $navItems = array_values(array_filter($navItems, fn (array $item): bool => in_array($item['route'], [
            'admin.orders.index',
            'admin.bookings.index',
        ], true)));
    }
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('layouts.partials.head', ['title' => $title ? $title.' - '.__('ui.admin.panel') : __('ui.admin.panel')])
    </head>
    <body class="min-h-screen" x-data="{ sidebarOpen: false }" @keydown.escape.window="sidebarOpen = false">
        <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[70] focus:rounded-full focus:bg-white focus:px-5 focus:py-2.5 focus:font-semibold focus:text-primary-800 focus:shadow-lift">
            {{ __('ui.skip_to_content') }}
        </a>

        <div class="min-h-screen lg:flex">
            <div x-show="sidebarOpen" x-cloak x-transition.opacity class="fixed inset-0 z-40 bg-primary-950/60 lg:hidden" @click="sidebarOpen = false"></div>

            <aside
                id="admin-sidebar"
                class="fixed inset-y-0 start-0 z-50 flex w-64 flex-col bg-primary-900 text-primary-100 transition-transform duration-200 lg:sticky lg:top-0 lg:h-screen lg:translate-x-0"
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            >
                <div class="flex h-16 shrink-0 items-center justify-between px-5">
                    <a href="{{ route('home') }}" class="rounded-xl" aria-label="{{ config('petshop.name') }}"><x-logo light /></a>
                    <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-full text-primary-100 hover:bg-primary-800 lg:hidden" @click="sidebarOpen = false">
                        <span class="sr-only">{{ __('ui.nav.close_menu') }}</span>
                        <x-icon name="close" class="h-6 w-6" />
                    </button>
                </div>

                <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4" aria-label="{{ __('ui.admin.menu') }}">
                    @foreach ($navItems as $item)
                        @if (Route::has($item['route']))
                            @php($active = request()->routeIs($item['route'].'*'))
                            <a
                                href="{{ route($item['route']) }}"
                                @if ($active) aria-current="page" @endif
                                @class([
                                    'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition',
                                    'bg-primary-700 text-white' => $active,
                                    'hover:bg-primary-800 hover:text-white' => ! $active,
                                ])
                            >
                                <x-icon :name="$item['icon']" class="h-5 w-5" />
                                {{ $item['label'] }}
                            </a>
                        @else
                            <span class="flex cursor-not-allowed items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-primary-300" aria-disabled="true">
                                <x-icon :name="$item['icon']" class="h-5 w-5" />
                                {{ $item['label'] }}
                                <span class="ms-auto rounded-full bg-primary-800 px-2 py-0.5 text-[11px] font-semibold text-primary-200">{{ __('ui.admin.coming_soon') }}</span>
                            </span>
                        @endif
                    @endforeach
                </nav>

                <div class="border-t border-primary-800 p-3">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition hover:bg-primary-800 hover:text-white">
                        <x-icon name="home" class="h-5 w-5" />
                        {{ __('ui.admin.view_store') }}
                    </a>
                </div>
            </aside>

            <div class="flex min-w-0 flex-1 flex-col">
                <div class="sticky top-0 z-30 flex h-16 items-center gap-3 bg-white/95 px-4 shadow-sm ring-1 ring-line backdrop-blur sm:px-6">
                    <button type="button" class="-ms-2 inline-flex h-11 w-11 items-center justify-center rounded-full text-primary-800 hover:bg-primary-100 lg:hidden" @click="sidebarOpen = true" :aria-expanded="sidebarOpen.toString()" aria-controls="admin-sidebar">
                        <span class="sr-only">{{ __('ui.nav.open_menu') }}</span>
                        <x-icon name="menu" class="h-6 w-6" />
                    </button>

                    <p class="truncate font-heading text-lg font-extrabold text-primary-900">{{ $title ?? __('ui.admin.panel') }}</p>

                    <div class="ms-auto">
                        @auth
                            <x-dropdown align="right" width="56">
                                <x-slot name="trigger">
                                    <button type="button" class="inline-flex items-center gap-2 rounded-full p-1 pe-3 text-primary-800 transition hover:bg-primary-100">
                                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-primary-700 font-heading text-base font-extrabold text-white" aria-hidden="true">
                                            {{ \Illuminate\Support\Str::of(auth()->user()->name)->trim()->substr(0, 1)->upper() }}
                                        </span>
                                        <span class="hidden max-w-[10rem] truncate text-sm font-semibold sm:block">{{ auth()->user()->name }}</span>
                                        <span class="sr-only sm:hidden">{{ __('ui.account.menu') }}</span>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <x-dropdown-link :href="auth()->user()->isAdmin() ? route('admin.dashboard') : route('admin.bookings.index')" icon="chart">
                                        {{ auth()->user()->isAdmin() ? __('ui.admin.nav.dashboard') : __('ui.admin.nav.bookings') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('profile.edit')" icon="user">{{ __('ui.account.profile') }}</x-dropdown-link>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <x-dropdown-link :href="route('logout')" icon="logout" onclick="event.preventDefault(); this.closest('form').submit();">
                                            {{ __('ui.account.logout') }}
                                        </x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        @endauth
                    </div>
                </div>

                <main id="main" class="flex-1 p-4 sm:p-6 lg:p-8">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <x-toast-container />

        @livewireScriptConfig
    </body>
</html>
