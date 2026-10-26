{{-- Renders toasts from the global Alpine store and turns session flashes (success, error, warning, info) into toasts. --}}
<div
    x-data
    class="pointer-events-none fixed inset-x-0 bottom-0 z-[60] flex flex-col items-center gap-2 p-4 sm:items-end sm:p-6"
    aria-live="polite"
    aria-atomic="false"
>
    @foreach (['success', 'error', 'warning', 'info'] as $type)
        @if (session($type))
            <span x-init="$store.toasts.add({ type: '{{ $type }}', message: @js(session($type)) })" hidden>{{ session($type) }}</span>
        @endif
    @endforeach

    <template x-for="toast in $store.toasts.items" :key="toast.id">
        <div
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="translate-y-3 opacity-0"
            x-transition:enter-end="translate-y-0 opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            :role="toast.type === 'error' ? 'alert' : 'status'"
            class="pointer-events-auto flex w-full max-w-sm items-start gap-3 rounded-2xl bg-white p-4 shadow-lift ring-1 ring-line"
        >
            <span
                class="mt-0.5 shrink-0"
                :class="{
                    'text-emerald-700': toast.type === 'success',
                    'text-red-700': toast.type === 'error',
                    'text-amber-700': toast.type === 'warning',
                    'text-sky-700': toast.type === 'info',
                }"
            >
                <x-icon name="check-circle" class="h-6 w-6" x-show="toast.type === 'success'" />
                <x-icon name="x-circle" class="h-6 w-6" x-show="toast.type === 'error'" x-cloak />
                <x-icon name="warning" class="h-6 w-6" x-show="toast.type === 'warning'" x-cloak />
                <x-icon name="info" class="h-6 w-6" x-show="toast.type === 'info'" x-cloak />
            </span>

            <p class="flex-1 text-sm font-medium text-ink" x-text="toast.message"></p>

            <button type="button" class="-m-1 shrink-0 rounded-full p-1 text-ink-muted transition hover:bg-cream-100 hover:text-ink" @click="$store.toasts.remove(toast.id)">
                <span class="sr-only">{{ __('ui.toast.dismiss') }}</span>
                <x-icon name="close" class="h-5 w-5" />
            </button>
        </div>
    </template>
</div>
