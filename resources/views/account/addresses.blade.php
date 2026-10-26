<x-app-layout :title="__('ui.account.addresses')">
    <div class="container-page grid gap-8 py-10 lg:grid-cols-[15rem_1fr]">
        @include('account.partials.sidebar', ['active' => 'addresses'])
        <section>
            <h1 class="text-3xl">{{ __('ui.account.addresses') }}</h1>
            <div class="mt-6 grid gap-4">
                @forelse ($addresses as $address)
                    <x-card class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <div class="flex gap-2 font-semibold">{{ $address->label }} @if ($address->is_default)<x-badge variant="primary">{{ __('ui.account.default_address') }}</x-badge>@endif</div>
                            <p class="mt-2">{{ $address->recipient_name }} · {{ $address->phone }}</p>
                            <p class="text-sm text-ink-muted">{{ $address->address_line }}, {{ $address->ward }}, {{ $address->district }}, {{ $address->province }}</p>
                        </div>
                        <form method="POST" action="{{ route('account.addresses.destroy', $address) }}">@csrf @method('DELETE')<x-button type="submit" variant="danger" size="sm">{{ __('ui.common.delete') }}</x-button></form>
                    </x-card>
                @empty
                    <x-empty-state :title="__('ui.account.no_addresses')" />
                @endforelse
            </div>
            <x-card class="mt-8">
                <h2 class="text-xl">{{ __('ui.account.add_address') }}</h2>
                <form method="POST" action="{{ route('account.addresses.store') }}" class="mt-5 grid gap-4 sm:grid-cols-2">@csrf
                    <x-input name="label" :label="__('ui.account.address_label')" required />
                    <x-input name="recipient_name" :label="__('ui.account.recipient')" required />
                    <x-input name="phone" :label="__('ui.auth.phone')" required />
                    <x-input name="province" :label="__('ui.account.province')" required />
                    <x-input name="district" :label="__('ui.account.district')" />
                    <x-input name="ward" :label="__('ui.account.ward')" />
                    <x-input name="address_line" :label="__('ui.account.address_line')" wrapper-class="sm:col-span-2" required />
                    <x-checkbox name="is_default" :label="__('ui.account.make_default')" />
                    <div><x-button type="submit">{{ __('ui.common.save') }}</x-button></div>
                </form>
            </x-card>
        </section>
    </div>
</x-app-layout>
