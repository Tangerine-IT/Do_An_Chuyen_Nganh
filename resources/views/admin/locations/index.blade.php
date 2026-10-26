<x-admin-layout :title="__('ui.map.admin_locations')">
    <div class="flex items-end justify-between gap-4">
        <h1 class="text-3xl">{{ __('ui.map.admin_locations') }}</h1>
        <x-button :href="route('admin.locations.create')">{{ __('ui.map.create') }}</x-button>
    </div>
    <form method="GET" class="mt-6 flex flex-wrap gap-3">
        <x-input name="q" :value="request('q')" label="{{ __('ui.search.submit') }}" />
        <x-select name="type" label="{{ __('ui.map.type') }}" class="min-w-48">
            <option value="">Tất cả</option>
            @foreach (['shop', 'vet', 'pet_friendly'] as $option)
                <option value="{{ $option }}" @selected(request('type') === $option)>{{ __('ui.map.'.$option) }}</option>
            @endforeach
        </x-select>
        <x-button type="submit" variant="secondary" class="self-end">{{ __('ui.search.submit') }}</x-button>
    </form>
    <x-card class="mt-6 overflow-x-auto p-0">
        <table class="w-full min-w-[58rem] text-left text-sm">
            <thead class="bg-cream-100"><tr><th class="px-5 py-3">{{ __('ui.map.name') }}</th><th class="px-5 py-3">{{ __('ui.map.type') }}</th><th class="px-5 py-3">{{ __('ui.map.address') }}</th><th class="px-5 py-3">{{ __('ui.admin.status') }}</th><th class="px-5 py-3">{{ __('ui.admin.actions') }}</th></tr></thead>
            <tbody class="divide-y divide-line">
                @forelse ($locations as $location)
                    <tr><td class="px-5 py-4 font-semibold">{{ $location->name }}</td><td class="px-5 py-4">{{ __('ui.map.'.$location->type) }}</td><td class="px-5 py-4">{{ $location->address }}</td><td class="px-5 py-4">{{ $location->is_active ? __('ui.admin.active') : __('ui.admin.inactive') }}</td><td class="flex gap-3 px-5 py-4"><a href="{{ route('admin.locations.edit', $location) }}" class="font-semibold text-primary-700">{{ __('ui.admin.edit') }}</a><form method="POST" action="{{ route('admin.locations.destroy', $location) }}">@csrf @method('DELETE')<button class="font-semibold text-red-700" onclick="return confirm('{{ __('ui.admin.confirm_delete') }}')">{{ __('ui.common.delete') }}</button></form></td></tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-ink-muted">{{ __('ui.map.empty') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </x-card>
    <div class="mt-6">{{ $locations->links() }}</div>
</x-admin-layout>
