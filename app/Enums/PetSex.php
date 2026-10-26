<?php

namespace App\Enums;

enum PetSex: string
{
    case Male = 'male';
    case Female = 'female';
    case Unknown = 'unknown';

    public function label(): string
    {
        return __('enums.pet_sex.'.$this->value);
    }
}
