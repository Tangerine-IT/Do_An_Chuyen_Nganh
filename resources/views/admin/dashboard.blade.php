<x-admin-layout :title="__('ui.admin.panel')">
    <div
        data-admin-dashboard
        data-revenue="{{ json_encode($revenue, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) }}"
        data-statuses="{{ json_encode($ordersByStatus, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) }}"
        data-products="{{ json_encode($topProducts, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) }}"
    >
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div><p class="eyebrow">{{ __('ui.admin.nav.dashboard') }}</p><h1 class="mt-2 text-3xl">{{ __('ui.admin.dashboard_title') }}</h1></div>
            <form method="GET"><label for="range" class="sr-only">{{ __('ui.admin.dashboard_range') }}</label><select id="range" name="range" onchange="this.form.submit()" class="form-control py-2"><option value="7d" @selected($revenueRange === '7d')>{{ __('ui.admin.range_7d') }}</option><option value="30d" @selected($revenueRange === '30d')>{{ __('ui.admin.range_30d') }}</option><option value="12m" @selected($revenueRange === '12m')>{{ __('ui.admin.range_12m') }}</option></select></form>
        </div>

        <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                [__('ui.admin.today_revenue'), format_vnd($summary['todayRevenue']), 'chart'],
                [__('ui.admin.month_revenue'), format_vnd($summary['monthRevenue']), ($summary['monthRevenueChange'] >= 0 ? '+' : '').$summary['monthRevenueChange'].'%'],
                [__('ui.admin.new_orders'), $summary['newOrders'], 'cart'],
                [__('ui.admin.pending_orders'), $summary['pendingOrders'], 'clock'],
                [__('ui.admin.new_customers'), $summary['newCustomers'], 'users'],
                [__('ui.admin.upcoming_bookings'), $summary['upcomingBookings'], 'calendar'],
            ] as [$label, $value, $icon])
                <x-card><div class="flex items-start justify-between gap-3"><p class="text-sm text-ink-muted">{{ $label }}</p><span class="rounded-full bg-primary-100 px-2 py-1 text-xs font-bold text-primary-800">{{ is_string($icon) && str_ends_with($icon, '%') ? $icon : '' }}</span></div><p class="mt-3 text-2xl font-bold text-primary-900">{{ $value }}</p></x-card>
            @endforeach
        </div>

        <div class="mt-6 grid gap-6 xl:grid-cols-[1.6fr_1fr]">
            <x-card><div class="flex items-center justify-between"><h2 class="text-xl">{{ __('ui.admin.revenue_chart') }}</h2><span class="text-sm text-ink-muted">{{ __('ui.admin.non_cancelled_note') }}</span></div><div class="mt-4 h-72"><canvas id="revenue-chart" aria-label="{{ __('ui.admin.revenue_chart') }}"></canvas></div></x-card>
            <x-card><h2 class="text-xl">{{ __('ui.admin.orders_by_status') }}</h2><div class="mt-4 h-72"><canvas id="status-chart" aria-label="{{ __('ui.admin.orders_by_status') }}"></canvas></div></x-card>
        </div>

        <div class="mt-6 grid gap-6 xl:grid-cols-[1.2fr_1fr]">
            <x-card><h2 class="text-xl">{{ __('ui.admin.top_products') }}</h2><div class="mt-4 h-80"><canvas id="products-chart" aria-label="{{ __('ui.admin.top_products') }}"></canvas></div></x-card>
            <x-card><h2 class="text-xl">{{ __('ui.admin.low_stock') }}</h2><div class="mt-4 divide-y divide-line">@forelse($lowStockProducts as $product)<a href="{{ route('admin.products.edit', $product) }}" class="flex items-center justify-between gap-3 py-3 hover:text-primary-700"><span class="truncate">{{ $product->name }}</span><span class="shrink-0 rounded-full bg-amber-100 px-2 py-1 text-xs font-bold text-amber-800">{{ $product->stock }}</span></a>@empty<p class="py-4 text-sm text-ink-muted">{{ __('ui.admin.no_low_stock') }}</p>@endforelse</div></x-card>
        </div>

        <x-card class="mt-6 overflow-x-auto p-0"><div class="p-5"><h2 class="text-xl">{{ __('ui.admin.recent_orders') }}</h2></div><table class="w-full min-w-[42rem] text-left text-sm"><thead class="bg-cream-100"><tr><th class="px-5 py-3">Mã đơn</th><th class="px-5 py-3">Khách hàng</th><th class="px-5 py-3">{{ __('ui.admin.status') }}</th><th class="px-5 py-3">Tổng</th></tr></thead><tbody class="divide-y divide-line">@forelse($recentOrders as $order)<tr><td class="px-5 py-4"><a class="font-semibold text-primary-700 hover:underline" href="{{ route('admin.orders.show', $order) }}">#{{ $order->order_number }}</a></td><td class="px-5 py-4">{{ $order->user?->name ?: '—' }}</td><td class="px-5 py-4">{{ $order->status->label() }}</td><td class="px-5 py-4 font-semibold">{{ format_vnd($order->total) }}</td></tr>@empty<tr><td colspan="4" class="px-5 py-8 text-center text-ink-muted">{{ __('ui.admin.no_orders') }}</td></tr>@endforelse</tbody></table></x-card>
    </div>
</x-admin-layout>
