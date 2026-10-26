@php
    $contact = config('petshop.contact');
@endphp

<footer class="mt-16 bg-primary-900 text-primary-100">
    <div class="container-page grid gap-10 py-14 sm:grid-cols-2 lg:grid-cols-4">
        <div class="space-y-4">
            <a href="{{ route('home') }}" class="inline-block rounded-xl" aria-label="{{ config('petshop.name') }}">
                <x-logo light />
            </a>
            <p class="text-sm leading-relaxed text-primary-200">{{ __('ui.footer.about') }}</p>
        </div>

        <nav aria-label="{{ __('ui.footer.shop') }}">
            <h2 class="mb-4 text-base text-white">{{ __('ui.footer.shop') }}</h2>
            <ul class="space-y-2.5 text-sm">
                <li><a href="{{ route('products.index') }}" class="transition hover:text-white hover:underline">{{ __('ui.nav.all_products') }}</a></li>
                @foreach ($navCategories as $category)
                    <li><a href="{{ route('categories.show', $category->slug) }}" class="transition hover:text-white hover:underline">{{ $category->name }}</a></li>
                @endforeach
            </ul>
        </nav>

        <nav aria-label="{{ __('ui.footer.explore') }}">
            <h2 class="mb-4 text-base text-white">{{ __('ui.footer.explore') }}</h2>
            <ul class="space-y-2.5 text-sm">
                <li><a href="{{ route('services.index') }}" class="transition hover:text-white hover:underline">{{ __('ui.nav.services') }}</a></li>
                <li><a href="{{ route('blog.index') }}" class="transition hover:text-white hover:underline">{{ __('ui.nav.blog') }}</a></li>
                <li><a href="{{ route('adoption.index') }}" class="transition hover:text-white hover:underline">{{ __('ui.nav.adoption') }}</a></li>
                <li><a href="{{ route('map') }}" class="transition hover:text-white hover:underline">{{ __('ui.nav.map') }}</a></li>
                @guest
                    <li><a href="{{ route('login') }}" class="transition hover:text-white hover:underline">{{ __('ui.account.login') }}</a></li>
                    <li><a href="{{ route('register') }}" class="transition hover:text-white hover:underline">{{ __('ui.account.register') }}</a></li>
                @else
                    <li><a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : (auth()->user()->isStaff() ? route('admin.bookings.index') : route('account')) }}" class="transition hover:text-white hover:underline">{{ __('ui.account.dashboard') }}</a></li>
                    @if (auth()->user()->isCustomer())
                        <li><a href="{{ route('account.pets') }}" class="transition hover:text-white hover:underline">{{ __('ui.account.pets') }}</a></li>
                    @endif
                @endguest
            </ul>
        </nav>

        <div>
            <h2 class="mb-4 text-base text-white">{{ __('ui.footer.contact') }}</h2>
            <ul class="space-y-3 text-sm">
                <li class="flex items-start gap-2.5">
                    <x-icon name="map-pin" class="mt-0.5 h-5 w-5 shrink-0 text-accent-300" />
                    <span>{{ $contact['address'] }}</span>
                </li>
                <li>
                    <a href="tel:{{ preg_replace('/\D/', '', $contact['phone']) }}" class="flex items-center gap-2.5 transition hover:text-white">
                        <x-icon name="phone" class="h-5 w-5 shrink-0 text-accent-300" />
                        {{ $contact['phone'] }}
                    </a>
                </li>
                <li>
                    <a href="mailto:{{ $contact['email'] }}" class="flex items-center gap-2.5 transition hover:text-white">
                        <x-icon name="mail" class="h-5 w-5 shrink-0 text-accent-300" />
                        {{ $contact['email'] }}
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="border-t border-primary-800">
        <p class="container-page py-5 text-center text-xs text-primary-200">
            &copy; {{ now()->year }} {{ config('petshop.name') }}. {{ __('ui.footer.rights') }}
        </p>
    </div>
</footer>
