<x-app-layout :title="__('ui.booking.title')">
    <livewire:booking-wizard :service-id="$service->id" :date="$date->toDateString()" :staff-id="request('staff')" :pet-id="request('pet')" />
</x-app-layout>
