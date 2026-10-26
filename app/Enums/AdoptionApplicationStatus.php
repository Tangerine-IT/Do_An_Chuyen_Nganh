<?php

namespace App\Enums;

enum AdoptionApplicationStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Withdrawn = 'withdrawn';

    public function label(): string
    {
        return __('enums.adoption_application_status.'.$this->value);
    }
}
