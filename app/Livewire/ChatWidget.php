<?php

namespace App\Livewire;

use App\Enums\ChatRole;
use App\Models\ChatMessage;
use App\Models\Product;
use App\Services\ChatService;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Component;

class ChatWidget extends Component
{
    public bool $open = false;

    public string $message = '';

    public string $conversationId = '';

    /** @var list<array{role: string, content: string, metadata?: array<string, mixed>}> */
    public array $messages = [];

    public function mount(): void
    {
        $this->conversationId = session('chat_conversation_id', (string) Str::uuid());
        session(['chat_conversation_id' => $this->conversationId]);
        $this->loadMessages();
    }

    public function send(ChatService $chatService): void
    {
        $this->validate(['message' => ['required', 'string', 'max:1000']]);
        $key = 'chat:'.request()->ip().':'.(auth()->id() ?: $this->conversationId);
        if (RateLimiter::tooManyAttempts($key, 10)) {
            $this->addError('message', __('ui.chat.rate_limited'));

            return;
        }
        RateLimiter::hit($key, 60);

        $text = trim($this->message);
        $this->append(ChatRole::User->value, $text);
        $pet = auth()->user()?->isCustomer()
            ? auth()->user()->pets()->find(session('current_pet_id'))
            : null;
        $answer = $chatService->answer($text, auth()->user(), $pet);
        $metadata = [
            'product_ids' => $answer['products']->modelKeys(),
            'warnings' => $answer['warnings'],
        ];
        $this->append(ChatRole::Assistant->value, $answer['answer'], $metadata);
        $this->message = '';
        $this->open = true;
    }

    public function newChat(): void
    {
        $this->conversationId = (string) Str::uuid();
        session(['chat_conversation_id' => $this->conversationId]);
        $this->messages = [];
    }

    public function render(): View
    {
        $ids = collect($this->messages)->flatMap(fn (array $message) => $message['metadata']['product_ids'] ?? [])->unique();
        $products = Product::query()->active()->inStock()->whereKey($ids)->with(['primaryImage', 'brand'])->get()->keyBy('id');

        return view('livewire.chat-widget', ['products' => $products]);
    }

    /** @param  array<string, mixed>  $metadata */
    private function append(string $role, string $content, array $metadata = []): void
    {
        $item = ['role' => $role, 'content' => $content, 'metadata' => $metadata];
        $this->messages[] = $item;
        $this->messages = array_slice($this->messages, -20);
        session(['chat_messages.'.$this->conversationId => $this->messages]);

        if (auth()->check()) {
            ChatMessage::query()->create([
                'user_id' => auth()->id(),
                'pet_id' => auth()->user()->pets()->find(session('current_pet_id'))?->id,
                'conversation_id' => $this->conversationId,
                'role' => $role,
                'content' => $content,
                'metadata' => $metadata,
            ]);
        }
    }

    private function loadMessages(): void
    {
        if (auth()->check()) {
            $this->messages = auth()->user()->chatMessages()
                ->where('conversation_id', $this->conversationId)
                ->oldest()
                ->limit(20)
                ->get()
                ->map(fn (ChatMessage $message): array => [
                    'role' => $message->role->value,
                    'content' => $message->content,
                    'metadata' => $message->metadata ?? [],
                ])->all();

            return;
        }

        $this->messages = session('chat_messages.'.$this->conversationId, []);
    }
}
