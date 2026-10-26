<x-app-layout :title="__('ui.loyalty.title')">
    <div class="container-page py-10">
        <h1 class="text-3xl">{{ __('ui.loyalty.title') }}</h1>
        <div class="mt-6 grid gap-4 sm:grid-cols-3">
            <x-card><p class="text-sm text-ink-muted">{{ __('ui.loyalty.balance') }}</p><p class="mt-1 text-3xl font-bold">{{ number_format(auth()->user()->loyalty_points) }}</p></x-card>
            <x-card><p class="text-sm text-ink-muted">{{ __('ui.loyalty.tier') }}</p><p class="mt-1 text-3xl font-bold capitalize">{{ $tier }}</p></x-card>
            <x-card><p class="text-sm text-ink-muted">{{ __('ui.loyalty.perks') }}</p><p class="mt-1">{{ implode(' · ', $perks) }}</p></x-card>
        </div>
        @if($badges)<div class="mt-6 flex flex-wrap gap-2">@foreach($badges as $badge)<span class="rounded-full bg-accent-100 px-3 py-1 text-sm font-semibold">{{ $badge }}</span>@endforeach</div>@endif
        <x-card class="mt-8 overflow-x-auto p-0"><table class="w-full min-w-[40rem] text-left text-sm"><thead class="bg-cream-100"><tr><th class="px-5 py-3">{{ __('ui.loyalty.date') }}</th><th class="px-5 py-3">{{ __('ui.loyalty.reason') }}</th><th class="px-5 py-3">{{ __('ui.loyalty.points') }}</th><th class="px-5 py-3">{{ __('ui.loyalty.balance') }}</th></tr></thead><tbody class="divide-y divide-line">@forelse($transactions as $transaction)<tr><td class="px-5 py-4">{{ $transaction->created_at?->format('d/m/Y H:i') }}</td><td class="px-5 py-4">{{ $transaction->description ?: $transaction->reason->label() }}</td><td class="px-5 py-4 font-semibold">{{ $transaction->points > 0 ? '+' : '' }}{{ $transaction->points }}</td><td class="px-5 py-4">{{ $transaction->balance_after }}</td></tr>@empty<tr><td colspan="4" class="px-5 py-8 text-center text-ink-muted">{{ __('ui.loyalty.empty') }}</td></tr>@endforelse</tbody></table></x-card>
        <div class="mt-6">{{ $transactions->links() }}</div>
    </div>
</x-app-layout>
