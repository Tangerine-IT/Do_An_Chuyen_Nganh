<x-app-layout :title="__('ui.account.health_book').' · '.$pet->name">
    @php
        $weightRecords = $pet->healthRecords->filter(fn ($record) => $record->weight_kg !== null)->sortBy('record_date')->values();
        $dueRecords = $pet->healthRecords->filter(fn ($record) => $record->next_due_date !== null)->sortBy('next_due_date');
    @endphp
    <div class="container-page py-10">
        <a href="{{ route('account.pets') }}" class="text-sm font-semibold text-primary-700 hover:underline">{{ __('ui.common.back') }}</a>
        <div class="mt-4 flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-primary-700">{{ $pet->species->label() }}</p>
                <h1 class="mt-1 text-3xl">{{ __('ui.account.health_book') }}: {{ $pet->name }}</h1>
                <p class="mt-2 text-ink-muted">{{ __('ui.account.health_book_intro') }}</p>
            </div>
            <x-button :href="route('services.index', ['pet' => $pet->id])" variant="accent">{{ __('ui.account.book_service') }}</x-button>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-[1.2fr_.8fr]">
            <x-card>
                <h2 class="text-xl">{{ __('ui.account.health_timeline') }}</h2>
                <div class="mt-5 space-y-4">
                    @forelse ($pet->healthRecords as $record)
                        <div class="rounded-xl border border-line p-4">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold">{{ $record->type->label() }}</p>
                                    <p class="text-sm text-ink-muted">{{ $record->record_date->format('d/m/Y') }}</p>
                                </div>
                                @if ($record->next_due_date)
                                        @php($healthStatus = $record->status())
                                        <x-badge variant="{{ $healthStatus === 'overdue' ? 'danger' : ($healthStatus === 'due_soon' ? 'warning' : 'success') }}">
                                            {{ __('ui.account.health_'.$healthStatus) }}
                                        </x-badge>
                                    @endif
                            </div>
                            @if ($record->weight_kg)<p class="mt-2 text-sm">{{ __('ui.account.pet_weight') }}: {{ $record->weight_kg }} kg</p>@endif
                            @if ($record->next_due_date)<p class="mt-1 text-sm text-ink-muted">{{ __('ui.account.next_due') }}: {{ $record->next_due_date->format('d/m/Y') }}</p>@endif
                            @if ($record->note)<p class="mt-2 whitespace-pre-line text-sm text-ink-muted">{{ $record->note }}</p>@endif
                            <form method="POST" action="{{ route('account.pets.health.destroy', [$pet, $record]) }}" class="mt-3">@csrf @method('DELETE')<button class="text-sm font-semibold text-red-700 hover:underline">{{ __('ui.common.delete') }}</button></form>
                        </div>
                    @empty
                        <x-empty-state :title="__('ui.account.health_empty')" />
                    @endforelse
                </div>
            </x-card>
            <x-card>
                <h2 class="text-xl">{{ __('ui.account.qr_title') }}</h2>
                <p class="mt-1 text-sm text-ink-muted">{{ __('ui.account.qr_intro') }}</p>
                <img src="{{ route('pets.qr', $pet->public_token) }}" alt="{{ __('ui.account.qr_alt') }}" class="mx-auto mt-4 h-40 w-40">
                <a href="{{ route('pets.public', $pet->public_token) }}" target="_blank" class="mt-3 block text-center text-sm font-semibold text-primary-700 hover:underline">{{ __('ui.account.open_public_page') }}</a>
                <form method="POST" action="{{ route('account.pets.sharing.update', $pet) }}" class="mt-5 space-y-2">@csrf @method('PATCH')
                    @foreach (['public_name' => 'public_name_label', 'public_photo' => 'public_photo_label', 'public_vaccinations' => 'public_vaccinations_label', 'public_contact' => 'public_contact_label'] as $field => $label)
                        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="{{ $field }}" value="1" @checked($pet->{$field})> {{ __('ui.account.'.$label) }}</label>
                    @endforeach
                    <x-button type="submit" size="sm" class="mt-2">{{ __('ui.common.save') }}</x-button>
                </form>
            </x-card>

            <div class="space-y-6">
                <x-card>
                    <h2 class="text-xl">{{ __('ui.account.health_upcoming') }}</h2>
                    <div class="mt-4 space-y-2">
                        @forelse ($dueRecords->take(5) as $record)
                            <div class="flex items-center justify-between gap-3 rounded-lg bg-cream-100 px-3 py-2 text-sm">
                                <span>{{ $record->type->label() }}</span>
                                <span class="{{ $record->status() === 'overdue' ? 'text-red-700' : ($record->status() === 'due_soon' ? 'text-amber-700' : 'text-emerald-700') }}">{{ $record->next_due_date->format('d/m/Y') }}</span>
                            </div>
                        @empty
                            <p class="text-sm text-ink-muted">{{ __('ui.account.health_no_due') }}</p>
                        @endforelse
                    </div>
                </x-card>
                <x-card>
                    <h2 class="text-xl">{{ __('ui.account.weight_chart') }}</h2>
                    <p class="mt-1 text-xs text-ink-muted">{{ __('ui.account.weight_chart_hint') }}</p>
                    @if ($weightRecords->isNotEmpty())
                        <div class="relative mt-4 h-52">
                            <canvas
                                id="pet-weight-chart"
                                data-labels='@json($weightRecords->map(fn ($record) => $record->record_date->format("d/m/Y"))->values())'
                                data-values='@json($weightRecords->map(fn ($record) => (float) $record->weight_kg)->values())'
                                aria-label="{{ __('ui.account.weight_chart') }}"
                            ></canvas>
                        </div>
                    @else
                        <p class="mt-4 text-sm text-ink-muted">{{ __('ui.account.weight_chart_empty') }}</p>
                    @endif
                </x-card>
            </div>
        </div>

        <x-card class="mt-8">
            <h2 class="text-xl">{{ __('ui.account.add_health_record') }}</h2>
            <form method="POST" action="{{ route('account.pets.health.store', $pet) }}" enctype="multipart/form-data" class="mt-5 grid gap-4 sm:grid-cols-2">@csrf
                <x-select name="type" :label="__('ui.account.health_type')" :options="$types" required />
                <x-input name="record_date" type="date" :label="__('ui.account.record_date')" :value="now()->toDateString()" required />
                <x-input name="next_due_date" type="date" :label="__('ui.account.next_due')" />
                <x-input name="weight_kg" type="number" min="0" step="0.01" :label="__('ui.account.pet_weight')" />
                <x-input name="attachment" type="file" accept="image/*" :label="__('ui.account.health_attachment')" />
                <x-textarea name="note" :label="__('ui.account.health_note')" wrapper-class="sm:col-span-2" />
                <div><x-button type="submit">{{ __('ui.common.save') }}</x-button></div>
            </form>
        </x-card>
    </div>
</x-app-layout>
