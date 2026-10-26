<x-admin-layout :title="$location->exists ? __('ui.map.edit') : __('ui.map.create')">
    <div class="max-w-3xl">
        <h1 class="text-3xl">{{ $location->exists ? __('ui.map.edit') : __('ui.map.create') }}</h1>
        <form method="POST" action="{{ $location->exists ? route('admin.locations.update', $location) : route('admin.locations.store') }}" class="mt-8 space-y-6">
            @csrf
            @if ($location->exists) @method('PUT') @endif
            <x-card class="grid gap-4 sm:grid-cols-2">
                <x-input name="name" :label="__('ui.map.name')" :value="$location->name" required />
                <x-select name="type" :label="__('ui.map.type')" required>
                    @foreach (['shop', 'vet', 'pet_friendly'] as $option)<option value="{{ $option }}" @selected($location->type === $option)>{{ __('ui.map.'.$option) }}</option>@endforeach
                </x-select>
                <x-input name="address" :label="__('ui.map.address')" :value="$location->address" wrapper-class="sm:col-span-2" required />
                <x-input name="phone" :label="__('ui.map.phone')" :value="$location->phone" />
                <x-input name="latitude" :label="__('ui.map.latitude')" type="number" step="any" :value="$location->latitude" required />
                <x-input name="longitude" :label="__('ui.map.longitude')" type="number" step="any" :value="$location->longitude" required />
                <x-input name="description" :label="__('ui.map.description')" :value="$location->description" wrapper-class="sm:col-span-2" />
                <x-checkbox name="is_active" :label="__('ui.map.active')" :checked="(bool) $location->is_active" />
            </x-card>
            <x-button type="submit">{{ __('ui.common.save') }}</x-button>
        </form>
    </div>
</x-admin-layout>
