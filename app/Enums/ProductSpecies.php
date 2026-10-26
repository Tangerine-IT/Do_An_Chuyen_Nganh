<?php

namespace App\Enums;

/**
 * Species a product is made for. Mirrors PetSpecies plus "all" (suitable for any pet).
 */
enum ProductSpecies: string
{
    case Dog = 'dog';
    case Cat = 'cat';
    case Bird = 'bird';
    case Fish = 'fish';
    case SmallPet = 'small_pet';
    case Other = 'other';
    case All = 'all';

    public function label(): string
    {
        return __('enums.product_species.'.$this->value);
    }
}
