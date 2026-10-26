<aside class="h-fit rounded-2xl bg-white p-3 shadow-soft ring-1 ring-line">
    <nav aria-label="{{ __('ui.account.navigation') }}" class="space-y-1">
        @foreach ([
            'overview' => [__('ui.account.overview'), route('account')],
            'profile' => [__('ui.account.profile'), route('profile.edit')],
            'addresses' => [__('ui.account.addresses'), route('account.addresses')],
            'orders' => [__('ui.account.orders'), route('orders.index')],
            'bookings' => [__('ui.account.bookings'), route('bookings.index')],
            'pets' => [__('ui.account.pets'), route('account.pets')],
            'wishlist' => [__('ui.account.wishlist'), route('wishlist.index')],
            'loyalty' => [__('ui.loyalty.title'), route('loyalty.index')],
            'feeding' => [__('ui.feeding.title'), route('feeding.index')],
        ] as $key => [$label, $href])
            <a href="{{ $href }}" @class(['block rounded-xl px-3 py-2.5 text-sm font-semibold', 'bg-primary-100 text-primary-800' => $active === $key, 'text-ink-muted hover:bg-cream-100' => $active !== $key])>{{ $label }}</a>
        @endforeach
    </nav>
</aside>
