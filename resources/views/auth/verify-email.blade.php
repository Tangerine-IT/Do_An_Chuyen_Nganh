<x-guest-layout :heading="__('ui.auth.verify_title')" :subheading="__('ui.auth.verify_text')">
    @if (session('status') == 'verification-link-sent')
        <x-auth-session-status class="mb-6" :status="__('ui.auth.verify_sent')" />
    @endif

    <div class="flex flex-wrap items-center justify-between gap-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-button type="submit">{{ __('ui.auth.verify_resend') }}</x-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="rounded text-sm font-semibold text-primary-700 hover:text-primary-900 hover:underline">
                {{ __('ui.auth.logout') }}
            </button>
        </form>
    </div>
</x-guest-layout>
