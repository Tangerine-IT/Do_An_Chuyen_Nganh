<x-app-layout :title="__('ui.coming_soon.title')">
    <div class="container-page py-16 sm:py-24">
        <x-empty-state class="mx-auto max-w-2xl" :title="__('ui.coming_soon.title')" :description="__('ui.coming_soon.description')">
            <x-button :href="route('home')" variant="accent">{{ __('ui.coming_soon.back_home') }}</x-button>
        </x-empty-state>
    </div>
</x-app-layout>
