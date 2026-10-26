<x-app-layout :title="__('ui.orders.title')">
    <div class="container-page grid gap-8 py-10 lg:grid-cols-[15rem_1fr]">
        @include('account.partials.sidebar', ['active' => 'orders'])
        <section><h1 class="text-3xl">{{ __('ui.orders.title') }}</h1>
            <div class="mt-6 space-y-3">
                @forelse ($orders as $order)
                    <a href="{{ route('orders.show', $order) }}" class="block rounded-2xl bg-white p-5 shadow-soft ring-1 ring-line transition hover:ring-primary-300">
                        <div class="flex flex-wrap justify-between gap-3"><strong>#{{ $order->order_number }}</strong><span class="text-sm text-ink-muted">{{ $order->status->label() }}</span></div>
                        <div class="mt-3 flex flex-wrap justify-between gap-3 text-sm"><span>{{ $order->placed_at?->format('d/m/Y H:i') }}</span><strong>{{ format_vnd($order->total) }}</strong></div>
                    </a>
                @empty
                    <x-empty-state :title="__('ui.account.orders_empty')" />
                @endforelse
            </div>
            <div class="mt-6">{{ $orders->links() }}</div>
        </section>
    </div>
</x-app-layout>
