<x-guest-layout :heading="__('ui.auth.register_title')" :subheading="__('ui.auth.register_text')">
    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <x-input name="name" :label="__('ui.auth.name')" required autofocus autocomplete="name" />
        <x-input name="phone" :label="__('ui.auth.phone')" required autocomplete="tel" />

        <x-input name="email" type="email" :label="__('ui.auth.email')" required autocomplete="username" />

        <x-input name="password" type="password" :label="__('ui.auth.password')" required autocomplete="new-password" />

        <x-input name="password_confirmation" type="password" :label="__('ui.auth.password_confirmation')" required autocomplete="new-password" />

        <x-button type="submit" size="lg" class="w-full">{{ __('ui.auth.register_button') }}</x-button>
    </form>

    <p class="mt-8 text-center text-sm text-ink-muted">
        {{ __('ui.auth.has_account') }}
        <a href="{{ route('login') }}" class="font-semibold text-primary-700 hover:text-primary-900 hover:underline">{{ __('ui.auth.login_button') }}</a>
    </p>
</x-guest-layout>
