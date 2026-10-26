<x-guest-layout :heading="__('ui.auth.confirm_title')" :subheading="__('ui.auth.confirm_text')">
    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf

        <x-input name="password" type="password" :label="__('ui.auth.password')" required autofocus autocomplete="current-password" />

        <x-button type="submit" size="lg" class="w-full">{{ __('ui.auth.confirm_button') }}</x-button>
    </form>
</x-guest-layout>
