<?php

namespace App\Enums;

enum ChatRole: string
{
    case User = 'user';
    case Assistant = 'assistant';

    public function label(): string
    {
        return __('enums.chat_role.'.$this->value);
    }
}
