<x-admin-layout :title="__('ui.booking.manual_create')">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div><p class="eyebrow">{{ __('ui.admin.nav.bookings') }}</p><h1 class="mt-2 text-3xl">{{ __('ui.booking.manual_create') }}</h1></div>
        <x-button :href="route('admin.bookings.index')" variant="secondary">{{ __('ui.common.back') }}</x-button>
    </div>
    <x-card class="mt-6">
        <form method="POST" action="{{ route('admin.bookings.store') }}" class="grid gap-4 sm:grid-cols-2">@csrf
            <x-select name="user_id" :label="__('ui.booking.customer')" :options="['' => __('ui.common.choose')] + $users->pluck('name', 'id')->all()" required />
            <x-select name="pet_id" :label="__('ui.booking.choose_pet')" :options="['' => __('ui.common.choose')] + \App\Models\Pet::query()->with('user')->orderBy('name')->get()->mapWithKeys(fn ($pet) => [$pet->id => $pet->name.' - '.$pet->user->name])->all()" required />
            <x-select name="service_id" :label="__('ui.booking.service')" :options="['' => __('ui.common.choose')] + $services->pluck('name', 'id')->all()" required />
            <x-select name="staff_id" :label="__('ui.booking.choose_staff')" :options="['' => __('ui.common.choose')] + $staff->pluck('name', 'id')->all()" required />
            <x-input name="starts_at" type="datetime-local" :label="__('ui.booking.choose_date')" required />
            <x-textarea name="notes" :label="__('ui.booking.notes')" wrapper-class="sm:col-span-2" />
            <div><x-button type="submit">{{ __('ui.booking.confirm') }}</x-button></div>
        </form>
    </x-card>
</x-admin-layout>
