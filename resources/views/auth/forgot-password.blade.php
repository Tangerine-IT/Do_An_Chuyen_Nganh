<x-guest-layout :heading="__('ui.auth.forgot_title')" :subheading="__('ui.auth.forgot_text')">
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <x-input name="email" type="email" :label="__('ui.auth.email')" required autofocus />

        <x-button type="submit" size="lg" class="w-full">{{ __('ui.auth.forgot_button') }}</x-button>
    </form>

    <p class="mt-8 text-center text-sm">
        <a href="{{ route('login') }}" class="font-semibold text-primary-700 hover:text-primary-900 hover:underline">{{ __('ui.auth.back_to_login') }}</a>
    </p>
</x-guest-layout>
