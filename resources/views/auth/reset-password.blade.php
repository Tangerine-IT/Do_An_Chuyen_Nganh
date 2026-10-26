<x-guest-layout :heading="__('ui.auth.reset_title')" :subheading="__('ui.auth.reset_text')">
    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <x-input name="email" type="email" :label="__('ui.auth.email')" :value="$request->email" required autofocus autocomplete="username" />

        <x-input name="password" type="password" :label="__('ui.auth.password')" required autocomplete="new-password" />

        <x-input name="password_confirmation" type="password" :label="__('ui.auth.password_confirmation')" required autocomplete="new-password" />

        <x-button type="submit" size="lg" class="w-full">{{ __('ui.auth.reset_button') }}</x-button>
    </form>
</x-guest-layout>
