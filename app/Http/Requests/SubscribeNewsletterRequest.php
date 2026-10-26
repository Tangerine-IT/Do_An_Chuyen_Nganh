<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubscribeNewsletterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email:rfc', 'max:254'],
        ];
    }

    /**
     * Send the visitor back to the newsletter strip so the error message is in view.
     */
    protected function getRedirectUrl(): string
    {
        return route('home').'#newsletter';
    }
}
