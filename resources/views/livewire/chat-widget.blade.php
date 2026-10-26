<div class="fixed bottom-5 end-5 z-50" x-data="{ open: @entangle('open') }">
    <div x-show="open" x-cloak class="mb-3 flex w-[min(92vw,24rem)] flex-col overflow-hidden rounded-2xl bg-white shadow-lift ring-1 ring-line">
        <div class="flex items-center justify-between bg-primary-800 px-4 py-3 text-white">
            <div>
                <p class="font-bold">{{ __('ui.chat.title') }}</p>
                <p class="text-xs text-primary-100">{{ __('ui.chat.disclaimer') }}</p>
            </div>
            <button type="button" class="rounded-full p-1 hover:bg-white/10" @click="open = false" aria-label="{{ __('ui.common.close') }}">×</button>
        </div>
        <div class="max-h-96 space-y-3 overflow-y-auto p-3" aria-live="polite">
            @forelse ($messages as $message)
                <div class="{{ $message['role'] === 'user' ? 'ms-8 bg-primary-100' : 'me-8 bg-cream-100' }} rounded-xl p-3 text-sm">
                    <p class="whitespace-pre-line">{{ $message['content'] }}</p>
                    @if ($message['role'] === 'assistant')
                        @foreach ($message['metadata']['warnings'] ?? [] as $warning)
                            <p class="mt-2 text-xs font-semibold text-amber-800">{{ $warning }}</p>
                        @endforeach
                        @foreach (($message['metadata']['product_ids'] ?? []) as $productId)
                            @if ($products->has($productId))
                                @php($product = $products->get($productId))
                                <a href="{{ route('products.show', $product->slug) }}" class="mt-2 block rounded-lg bg-white p-2 text-xs font-semibold text-primary-800 ring-1 ring-line hover:ring-primary-500">
                                    {{ $product->name }}
                                    <span class="mt-1 block font-normal text-ink-muted">{{ format_vnd($product->current_price) }}</span>
                                </a>
                                <form method="POST" action="{{ route('cart.items.store') }}" class="mt-1">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="w-full rounded-lg bg-accent-500 px-2 py-1 text-xs font-semibold text-primary-950 hover:bg-accent-400">{{ __('ui.chat.add_to_cart') }}</button>
                                </form>
                            @endif
                        @endforeach
                    @endif
                </div>
            @empty
                <p class="text-sm text-ink-muted">{{ __('ui.chat.greeting') }}</p>
            @endforelse
        </div>
        <div class="border-t border-line p-3">
            <div class="mb-2 flex flex-wrap gap-1">
                @foreach (['food' => __('ui.chat.suggestion_food'), 'allergy' => __('ui.chat.suggestion_allergy'), 'vet' => __('ui.chat.suggestion_vet')] as $suggestion)
                    <button type="button" wire:click="$set('message', @js($suggestion))" class="rounded-full bg-cream-100 px-2 py-1 text-xs text-primary-800">{{ $suggestion }}</button>
                @endforeach
            </div>
            <form wire:submit="send" class="flex gap-2">
                <input wire:model="message" type="text" maxlength="1000" class="form-control min-w-0 flex-1" placeholder="{{ __('ui.chat.placeholder') }}" aria-label="{{ __('ui.chat.placeholder') }}">
                <x-button type="submit" size="sm" wire:loading.attr="disabled">{{ __('ui.chat.send') }}</x-button>
            </form>
            @error('message')<p class="mt-1 text-xs text-red-700">{{ $message }}</p>@enderror
            <button type="button" wire:click="newChat" class="mt-2 text-xs font-semibold text-ink-muted hover:text-primary-700">{{ __('ui.chat.new_chat') }}</button>
        </div>
    </div>
    <button type="button" @click="open = !open" class="flex h-14 w-14 items-center justify-center rounded-full bg-accent-500 text-2xl text-primary-950 shadow-lift hover:bg-accent-400" aria-label="{{ __('ui.chat.open') }}">💬</button>
</div>
