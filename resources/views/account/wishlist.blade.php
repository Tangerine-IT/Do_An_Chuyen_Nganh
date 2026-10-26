<x-app-layout :title="__('ui.account.wishlist')">
    <div class="container-page grid gap-8 py-10 lg:grid-cols-[15rem_1fr]">
        @include('account.partials.sidebar', ['active' => 'wishlist'])
        <section><h1 class="text-3xl">{{ __('ui.account.wishlist') }}</h1>
            <div class="mt-6 grid grid-cols-2 gap-3 sm:gap-5 lg:grid-cols-3">
                @forelse($products as $wishlist)<x-product-card :product="$wishlist->product" />@empty<x-empty-state class="col-span-full" :title="__('ui.wishlist.empty')" />@endforelse
            </div>
            <div class="mt-6">{{ $products->links() }}</div>
        </section>
    </div>
</x-app-layout>
