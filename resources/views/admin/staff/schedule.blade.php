<x-admin-layout :title="__('ui.booking.staff_schedule')">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div><p class="eyebrow">{{ $staff->name }}</p><h1 class="mt-2 text-3xl">{{ __('ui.booking.staff_schedule') }}</h1></div>
        <x-button :href="route('admin.bookings.index')" variant="secondary">{{ __('ui.common.back') }}</x-button>
    </div>
    <x-card class="mt-6">
        <form method="POST" action="{{ route('admin.staff.schedule.update', $staff) }}" class="space-y-4">@csrf @method('PUT')
            @foreach ([1 => 'Thứ hai', 2 => 'Thứ ba', 3 => 'Thứ tư', 4 => 'Thứ năm', 5 => 'Thứ sáu', 6 => 'Thứ bảy', 7 => 'Chủ nhật'] as $weekday => $label)
                @php($schedule = $schedules->get($weekday))
                <div class="grid items-end gap-3 border-b border-line pb-4 sm:grid-cols-[8rem_1fr_1fr_auto]">
                    <strong>{{ $label }}</strong>
                    <x-input name="schedule[{{ $weekday }}][open_time]" type="time" label="Mở cửa" :value="$schedule?->open_time" />
                    <x-input name="schedule[{{ $weekday }}][close_time]" type="time" label="Đóng cửa" :value="$schedule?->close_time" />
                    <label class="flex items-center gap-2 pb-2 text-sm"><input type="checkbox" name="schedule[{{ $weekday }}][is_day_off]" value="1" @checked($schedule?->is_day_off)> Nghỉ</label>
                </div>
            @endforeach
            <x-button type="submit">{{ __('ui.common.save') }}</x-button>
        </form>
    </x-card>
</x-admin-layout>
