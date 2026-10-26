<x-admin-layout :title="__('ui.coupon.create')">
    <h1 class="text-3xl">{{ $coupon->exists ? __('ui.coupon.edit') : __('ui.coupon.create') }}</h1>
    <x-card class="mt-6"><form method="POST" action="{{ $coupon->exists ? route('admin.coupons.update', $coupon) : route('admin.coupons.store') }}" class="grid gap-4 sm:grid-cols-2">@csrf @if($coupon->exists) @method('PUT') @endif
        <x-input name="code" :label="__('ui.coupon.code')" :value="$coupon->code" required />
        <x-select name="category_id" :label="__('ui.coupon.category')" :options="['' => __('ui.common.all')] + $categories->pluck('name', 'id')->all()" :value="$coupon->category_id" />
        <x-select name="type" :label="__('ui.coupon.type')" :options="collect($types)->mapWithKeys(fn ($type) => [$type->value => $type->label()])->all()" :value="$coupon->type?->value" required />
        <x-input name="value" type="number" :label="__('ui.coupon.value')" :value="$coupon->value" required />
        <x-input name="min_order_total" type="number" :label="__('ui.coupon.minimum')" :value="$coupon->min_order_total ?? 0" required />
        <x-input name="max_discount" type="number" :label="__('ui.coupon.max_discount')" :value="$coupon->max_discount" />
        <x-input name="usage_limit" type="number" :label="__('ui.coupon.usage_limit')" :value="$coupon->usage_limit" />
        <x-input name="starts_at" type="datetime-local" :label="__('ui.coupon.starts_at')" :value="$coupon->starts_at?->format('Y-m-d\TH:i')" />
        <x-input name="expires_at" type="datetime-local" :label="__('ui.coupon.expires_at')" :value="$coupon->expires_at?->format('Y-m-d\TH:i')" />
        <x-checkbox name="is_active" :label="__('ui.coupon.active')" :checked="$coupon->is_active" />
        <div><x-button type="submit">{{ __('ui.common.save') }}</x-button></div>
    </form></x-card>
</x-admin-layout>
