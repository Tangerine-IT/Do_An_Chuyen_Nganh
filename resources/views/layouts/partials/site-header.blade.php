@php
    $user = auth()->user();
    $phone = config('petshop.contact.phone');
@endphp

<header
    class="sticky top-0 z-40 bg-cream-50/95 shadow-sm ring-1 ring-line backdrop-blur"
    x-data="{ mobileOpen: false }"
    @keydown.escape.window="mobileOpen = false"
>
    {{-- Announcement strip --}}
    <div class="hidden bg-primary-800 text-primary-50 sm:block">
        <div class="container-page flex h-9 items-center justify-between text-xs">
            <p>{{ __('ui.announcement') }}</p>
            <a href="tel:{{ preg_replace('/\D/', '', $phone) }}" class="inline-flex items-center gap-1.5 font-semibold hover:text-white">
                <x-icon name="phone" class="h-4 w-4" />
                {{ $phone }}
            </a>
        </div>
    </div>

    {{-- Main bar --}}
    <div class="container-page flex h-16 items-center gap-2 sm:gap-3 lg:h-20 lg:gap-6">
        <button
            type="button"
            class="-ms-2 inline-flex h-11 w-11 items-center justify-center rounded-full text-primary-800 hover:bg-primary-100 lg:hidden"
            @click="mobileOpen = true"
            :aria-expanded="mobileOpen.toString()"
            aria-controls="mobile-menu"
        >
            <span class="sr-only">{{ __('ui.nav.open_menu') }}</span>
            <x-icon name="menu" class="h-6 w-6" />
        </button>

        <a href="{{ route('home') }}" class="shrink-0 rounded-xl" aria-label="{{ config('petshop.name') }}">
            <x-logo />
        </a>

        <form action="{{ route('products.index') }}" method="GET" role="search" class="mx-auto hidden w-full max-w-xl flex-1 md:block">
            <label for="site-search" class="sr-only">{{ __('ui.search.label') }}</label>
            <div class="relative">
                <x-icon name="search" class="pointer-events-none absolute start-4 top-1/2 h-5 w-5 -translate-y-1/2 text-ink-muted" />
                <input
                    id="site-search"
                    type="search"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="{{ __('ui.search.placeholder') }}"
                    class="form-control rounded-full bg-white ps-11 pe-24"
                >
                <button type="submit" class="absolute end-1.5 top-1/2 -translate-y-1/2 rounded-full bg-primary-700 px-4 py-1.5 text-sm font-semibold text-white transition hover:bg-primary-800">
                    {{ __('ui.search.submit') }}
                </button>
            </div>
        </form>

        <div class="ms-auto flex items-center gap-1 sm:gap-2 md:ms-0">
            <a href="{{ route('cart.index') }}" class="relative inline-flex h-11 w-11 items-center justify-center rounded-full text-primary-800 transition hover:bg-primary-100">
                <x-icon name="cart" class="h-6 w-6" />
                <span class="sr-only">{{ __('ui.cart.label') }}</span>
                @if ($cartCount > 0)
                    <span class="absolute -end-0.5 -top-0.5 flex h-5 min-w-5 items-center justify-center rounded-full bg-accent-500 px-1 text-xs font-bold text-primary-950" title="{{ __('ui.cart.count', ['count' => $cartCount]) }}">
                        {{ $cartCount > 99 ? '99+' : $cartCount }}
                    </span>
                @endif
            </a>

            @auth
                <x-dropdown align="right" width="80">
                    <x-slot name="trigger">
                        <button type="button" class="relative inline-flex h-11 w-11 items-center justify-center rounded-full text-primary-800 transition hover:bg-primary-100" aria-label="{{ __('ui.account.notifications') }}">
                            <x-icon name="bell" class="h-5 w-5" />
                            @if ($notificationCount > 0)
                                <span class="absolute end-0 top-0 flex h-5 min-w-5 items-center justify-center rounded-full bg-accent-500 px-1 text-xs font-bold text-primary-950">{{ $notificationCount > 99 ? '99+' : $notificationCount }}</span>
                            @endif
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <p class="border-b border-line px-4 pb-2 pt-1 text-sm font-bold text-primary-900">{{ __('ui.account.notifications') }}</p>
                        @forelse ($unreadNotifications as $notification)
                            <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                @csrf
                                <button type="submit" class="block w-full border-b border-line px-4 py-3 text-start text-sm hover:bg-primary-50">
                                    <span class="font-semibold">{{ data_get($notification->data, 'pet_name') }}</span>
                                    <span class="mt-1 block text-xs text-ink-muted">{{ __('ui.account.health_reminder_short') }}</span>
                                </button>
                            </form>
                        @empty
                            <p class="px-4 py-4 text-sm text-ink-muted">{{ __('ui.account.notifications_empty') }}</p>
                        @endforelse
                    </x-slot>
                </x-dropdown>
                <x-dropdown align="right" width="56">
                    <x-slot name="trigger">
                        <button type="button" class="inline-flex items-center gap-2 rounded-full p-1 pe-2 text-primary-800 transition hover:bg-primary-100 sm:pe-3">
                            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-primary-700 font-heading text-base font-extrabold text-white" aria-hidden="true">
                                {{ \Illuminate\Support\Str::of($user->name)->trim()->substr(0, 1)->upper() }}
                            </span>
                            <span class="sr-only sm:not-sr-only sm:max-w-[8rem] sm:truncate sm:text-sm sm:font-semibold">{{ $user->name }}</span>
                            <x-icon name="chevron-down" class="hidden h-4 w-4 sm:block" />
                            <span class="sr-only sm:hidden">{{ __('ui.account.menu') }}</span>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="border-b border-line px-4 pb-2 pt-1">
                            <p class="truncate text-sm font-bold text-primary-900">{{ $user->name }}</p>
                            <p class="truncate text-xs text-ink-muted">{{ $user->email }}</p>
                        </div>
                        <x-dropdown-link :href="$user->isAdmin() ? route('admin.dashboard') : ($user->isStaff() ? route('admin.bookings.index') : route('account'))" icon="user">{{ __('ui.account.dashboard') }}</x-dropdown-link>
                        <x-dropdown-link :href="route('profile.edit')" icon="home">{{ __('ui.account.profile') }}</x-dropdown-link>
                        @if ($user->isCustomer())
                            <x-dropdown-link :href="route('account.pets')" icon="heart">{{ __('ui.account.pets') }}</x-dropdown-link>
                            @if ($accountPets->isNotEmpty())
                                <div class="border-t border-line px-4 pb-1 pt-2">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-ink-muted">{{ __('ui.account.current_pet') }}</p>
                                    @foreach ($accountPets as $pet)
                                        <form method="POST" action="{{ route('account.pets.current', $pet) }}">
                                            @csrf
                                            <button type="submit" class="mt-1 flex w-full items-center gap-2 rounded-lg px-2 py-1.5 text-start text-sm hover:bg-primary-50">
                                                <span class="h-2 w-2 rounded-full {{ $currentPet?->is($pet) ? 'bg-accent-500' : 'bg-line' }}"></span>
                                                {{ $pet->name }}
                                            </button>
                                        </form>
                                    @endforeach
                                    @if ($currentPet)
                                        <form method="POST" action="{{ route('account.pets.current.clear') }}">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="mt-1 px-2 py-1 text-xs font-semibold text-ink-muted hover:text-primary-700">{{ __('ui.account.clear_current_pet') }}</button>
                                        </form>
                                    @endif
                                </div>
                            @endif
                        @endif

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" icon="logout" onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('ui.account.logout') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            @else
                <x-button :href="route('login')" variant="ghost" size="sm" class="hidden sm:inline-flex">{{ __('ui.account.login') }}</x-button>
                <x-button :href="route('register')" size="sm" class="hidden sm:inline-flex">{{ __('ui.account.register') }}</x-button>
                <a href="{{ route('login') }}" class="inline-flex h-11 w-11 items-center justify-center rounded-full text-primary-800 transition hover:bg-primary-100 sm:hidden">
                    <x-icon name="user" class="h-6 w-6" />
                    <span class="sr-only">{{ __('ui.account.login') }}</span>
                </a>
            @endauth
        </div>
    </div>

    {{-- Mobile search --}}
    <form action="{{ route('products.index') }}" method="GET" role="search" class="container-page pb-3 md:hidden">
        <label for="site-search-mobile" class="sr-only">{{ __('ui.search.label') }}</label>
        <div class="relative">
            <x-icon name="search" class="pointer-events-none absolute start-4 top-1/2 h-5 w-5 -translate-y-1/2 text-ink-muted" />
            <input id="site-search-mobile" type="search" name="q" value="{{ request('q') }}" placeholder="{{ __('ui.search.placeholder') }}" class="form-control rounded-full bg-white ps-11">
        </div>
    </form>

    {{-- Desktop category menu --}}
    <nav class="hidden border-t border-line lg:block" aria-label="{{ __('ui.nav.categories') }}">
        <ul class="container-page flex h-12 items-center gap-1 text-sm font-semibold text-primary-900">
            <li>
                <a href="{{ route('products.index') }}" class="rounded-full px-4 py-2 transition hover:bg-primary-100">{{ __('ui.nav.all_products') }}</a>
            </li>

            @foreach ($navCategories as $category)
                <li
                    class="relative"
                    x-data="{ open: false }"
                    @mouseenter="open = true"
                    @mouseleave="open = false"
                    @focusin="open = true"
                    @focusout="if (! $el.contains($event.relatedTarget)) open = false"
                    @keydown.escape="open = false"
                >
                    <a href="{{ route('categories.show', $category->slug) }}" class="inline-flex items-center gap-1 rounded-full px-4 py-2 transition hover:bg-primary-100">
                        {{ $category->name }}
                        @if ($category->children->isNotEmpty())
                            <x-icon name="chevron-down" class="h-4 w-4" />
                        @endif
                    </a>

                    @if ($category->children->isNotEmpty())
                        <div x-show="open" x-cloak x-transition.opacity.duration.150ms class="absolute start-0 top-full z-50 w-64 pt-2">
                            <ul class="rounded-2xl bg-white p-2 shadow-lift ring-1 ring-line">
                                @foreach ($category->children as $child)
                                    <li>
                                        <a href="{{ route('categories.show', $child->slug) }}" class="block rounded-xl px-3 py-2 font-medium text-ink transition hover:bg-primary-50 hover:text-primary-800">{{ $child->name }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </li>
            @endforeach

            <li class="ms-auto"><a href="{{ route('services.index') }}" class="rounded-full px-4 py-2 transition hover:bg-primary-100">{{ __('ui.nav.services') }}</a></li>
            <li><a href="{{ route('blog.index') }}" class="rounded-full px-4 py-2 transition hover:bg-primary-100">{{ __('ui.nav.blog') }}</a></li>
            <li><a href="{{ route('adoption.index') }}" class="rounded-full px-4 py-2 transition hover:bg-primary-100">{{ __('ui.nav.adoption') }}</a></li>
        </ul>
    </nav>

    {{-- Mobile menu --}}
    <div x-show="mobileOpen" x-cloak class="fixed inset-0 z-50 lg:hidden" id="mobile-menu" role="dialog" aria-modal="true" aria-label="{{ __('ui.nav.categories') }}">
        <div class="absolute inset-0 bg-primary-950/60 backdrop-blur-sm" @click="mobileOpen = false" x-transition.opacity></div>

        <div
            class="absolute inset-y-0 start-0 flex w-80 max-w-[85%] flex-col overflow-y-auto bg-cream-50 shadow-lift"
            x-show="mobileOpen"
            x-trap.noscroll="mobileOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
        >
            <div class="flex h-16 shrink-0 items-center justify-between border-b border-line px-4">
                <x-logo />
                <button type="button" class="inline-flex h-11 w-11 items-center justify-center rounded-full text-primary-800 hover:bg-primary-100" @click="mobileOpen = false">
                    <span class="sr-only">{{ __('ui.nav.close_menu') }}</span>
                    <x-icon name="close" class="h-6 w-6" />
                </button>
            </div>

            <nav class="flex-1 space-y-1 p-4 text-primary-900" aria-label="{{ __('ui.nav.main') }}">
                <a href="{{ route('products.index') }}" class="block rounded-xl px-3 py-3 font-semibold hover:bg-primary-100">{{ __('ui.nav.all_products') }}</a>

                @foreach ($navCategories as $category)
                    <details class="group rounded-xl">
                        <summary class="flex cursor-pointer list-none items-center justify-between rounded-xl px-3 py-3 font-semibold hover:bg-primary-100 [&::-webkit-details-marker]:hidden">
                            {{ $category->name }}
                            <x-icon name="chevron-down" class="h-5 w-5 transition group-open:rotate-180" />
                        </summary>
                        <ul class="ms-3 space-y-0.5 border-s-2 border-primary-200 py-1 ps-3">
                            <li><a href="{{ route('categories.show', $category->slug) }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-primary-700 hover:bg-primary-100">{{ __('ui.common.view_all') }}</a></li>
                            @foreach ($category->children as $child)
                                <li><a href="{{ route('categories.show', $child->slug) }}" class="block rounded-lg px-3 py-2 text-sm hover:bg-primary-100">{{ $child->name }}</a></li>
                            @endforeach
                        </ul>
                    </details>
                @endforeach

                <div class="my-2 border-t border-line"></div>
                <a href="{{ route('services.index') }}" class="block rounded-xl px-3 py-3 font-semibold hover:bg-primary-100">{{ __('ui.nav.services') }}</a>
                <a href="{{ route('blog.index') }}" class="block rounded-xl px-3 py-3 font-semibold hover:bg-primary-100">{{ __('ui.nav.blog') }}</a>
                <a href="{{ route('adoption.index') }}" class="block rounded-xl px-3 py-3 font-semibold hover:bg-primary-100">{{ __('ui.nav.adoption') }}</a>
            </nav>

            @guest
                <div class="grid grid-cols-2 gap-3 border-t border-line p-4">
                    <x-button :href="route('login')" variant="secondary">{{ __('ui.account.login') }}</x-button>
                    <x-button :href="route('register')">{{ __('ui.account.register') }}</x-button>
                </div>
            @endguest
        </div>
    </div>
</header>
