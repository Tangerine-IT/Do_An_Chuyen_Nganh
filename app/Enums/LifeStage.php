<?php

namespace App\Enums;

/**
 * Life stage a product targets. "Young" covers puppy/kitten/chick/fry.
 */
enum LifeStage: string
{
    case Young = 'young';
    case Adult = 'adult';
    case Senior = 'senior';
    case All = 'all';

    public function label(): string
    {
        return __('enums.life_stage.'.$this->value);
    }
}
