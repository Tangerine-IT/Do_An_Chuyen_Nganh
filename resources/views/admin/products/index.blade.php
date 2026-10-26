<x-admin-layout :title="__('ui.admin.products_title')">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div><p class="eyebrow">{{ __('ui.admin.nav.products') }}</p><h1 class="mt-2 text-3xl">{{ __('ui.admin.products_title') }}</h1></div>
        <x-button :href="route('admin.products.create')">{{ __('ui.admin.create_product') }}</x-button>
    </div>
    <form method="GET" class="mt-6 flex flex-wrap gap-3">
        <x-input name="q" :value="request('q')" :label="__('ui.admin.search')" wrapper-class="min-w-64 flex-1" />
        <x-select name="status" :label="__('ui.admin.status')" :options="['' => __('ui.admin.all'), '1' => __('ui.admin.active'), '0' => __('ui.admin.inactive')]" :value="request('status')" />
        <x-button type="submit" variant="secondary" class="self-end">{{ __('ui.search.submit') }}</x-button>
    </form>
    <x-card class="mt-6 overflow-x-auto p-0">
        <table class="w-full min-w-[48rem] text-left text-sm"><thead class="bg-cream-100 text-ink-muted"><tr><th class="px-5 py-3">{{ __('ui.admin.nav.products') }}</th><th class="px-5 py-3">{{ __('ui.admin.status') }}</th><th class="px-5 py-3">Tồn kho</th><th class="px-5 py-3">{{ __('ui.admin.actions') }}</th></tr></thead><tbody class="divide-y divide-line">
            @forelse ($products as $product)<tr><td class="px-5 py-4"><strong>{{ $product->name }}</strong><p class="text-xs text-ink-muted">{{ $product->sku ?: '—' }}</p></td><td class="px-5 py-4">{{ $product->is_active ? __('ui.admin.active') : __('ui.admin.inactive') }}</td><td class="px-5 py-4">{{ $product->stock }}</td><td class="px-5 py-4"><div class="flex gap-3"><a class="font-semibold text-primary-700 hover:underline" href="{{ route('admin.products.edit', $product) }}">{{ __('ui.admin.edit') }}</a><form method="POST" action="{{ route('admin.products.destroy', $product) }}">@csrf @method('DELETE')<button type="submit" class="font-semibold text-red-700 hover:underline" onclick="return confirm('{{ __('ui.admin.confirm_delete') }}')">{{ __('ui.common.delete') }}</button></form></div></td></tr>@empty<tr><td colspan="4" class="px-5 py-10 text-center text-ink-muted">{{ __('ui.catalog.empty_title') }}</td></tr>@endforelse
        </tbody></table>
    </x-card><div class="mt-6">{{ $products->links() }}</div>
</x-admin-layout>
