<x-guest-layout :heading="__('ui.auth.login_title')" :subheading="__('ui.auth.login_text')">
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <x-input name="email" type="email" :label="__('ui.auth.email')" required autofocus autocomplete="username" />

        <x-input name="password" type="password" :label="__('ui.auth.password')" required autocomplete="current-password" />

        <div class="flex flex-wrap items-center justify-between gap-3">
            <x-checkbox name="remember" id="remember_me" :label="__('ui.auth.remember')" />

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="rounded text-sm font-semibold text-primary-700 hover:text-primary-900 hover:underline">
                    {{ __('ui.auth.forgot') }}
                </a>
            @endif
        </div>

        <x-button type="submit" size="lg" class="w-full">{{ __('ui.auth.login_button') }}</x-button>
    </form>

    <p class="mt-8 text-center text-sm text-ink-muted">
        {{ __('ui.auth.no_account') }}
        <a href="{{ route('register') }}" class="font-semibold text-primary-700 hover:text-primary-900 hover:underline">{{ __('ui.auth.register_button') }}</a>
    </p>
</x-guest-layout>
