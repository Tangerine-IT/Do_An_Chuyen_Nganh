<div class="container-page max-w-4xl py-10 sm:py-14">
    <a href="{{ route('services.show', $service) }}" class="text-sm font-semibold text-primary-700 hover:underline">{{ $service->name }}</a>
    <h1 class="mt-3 text-3xl">{{ __('ui.booking.title') }}</h1>
    <div class="mt-6 grid gap-4 rounded-2xl bg-cream-100 p-5 sm:grid-cols-2">
        <x-input wire:model.live="date" name="date" type="date" :label="__('ui.booking.choose_date')" min="{{ today()->toDateString() }}" />
        <x-select wire:model.live="staffId" name="staffId" :label="__('ui.booking.choose_staff')" :options="['' => __('ui.booking.any_staff')] + $staff->pluck('name', 'id')->all()" />
    </div>
    <form wire:submit="submit" class="mt-6 space-y-6">
        <x-card><h2 class="text-xl">{{ __('ui.booking.choose_pet') }}</h2><div class="mt-4 grid gap-3 sm:grid-cols-2">@forelse($pets as $pet)<label class="flex cursor-pointer gap-3 rounded-xl border border-line p-4 has-[:checked]:border-primary-700"><input wire:model="petId" type="radio" value="{{ $pet->id }}" required><span>{{ $pet->name }}</span></label>@empty<p class="text-sm text-ink-muted">Bạn cần tạo hồ sơ thú cưng trước khi đặt lịch.</p>@endforelse</div>@error('petId')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror</x-card>
        <x-card><h2 class="text-xl">{{ __('ui.booking.choose_time') }}</h2><div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4">@forelse($slots as $slot)<label class="cursor-pointer rounded-xl border border-line p-3 text-center has-[:checked]:border-primary-700"><input wire:model="startsAt" class="sr-only" type="radio" value="{{ $slot->format('Y-m-d\TH:i') }}"><span>{{ $slot->format('H:i') }}</span></label>@empty<p class="col-span-full text-sm text-ink-muted">{{ __('ui.booking.no_slots') }}</p>@endforelse</div>@error('startsAt')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror</x-card>
        <x-textarea wire:model="notes" name="notes" :label="__('ui.booking.notes')" />
        <x-button type="submit" variant="accent">{{ __('ui.booking.confirm') }}</x-button>
    </form>
</div>
