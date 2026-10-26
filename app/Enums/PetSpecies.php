<?php

namespace App\Enums;

/**
 * Species of a pet. Products use ProductSpecies, which adds an "all" case.
 */
enum PetSpecies: string
{
    case Dog = 'dog';
    case Cat = 'cat';
    case Bird = 'bird';
    case Fish = 'fish';
    case SmallPet = 'small_pet';
    case Other = 'other';

    public function label(): string
    {
        return __('enums.pet_species.'.$this->value);
    }
}
