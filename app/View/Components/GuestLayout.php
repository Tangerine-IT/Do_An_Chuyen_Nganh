<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * Layout for authentication pages: <x-guest-layout heading="..." subheading="...">.
 */
class GuestLayout extends Component
{
    public function __construct(
        public ?string $heading = null,
        public ?string $subheading = null,
    ) {}

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.guest');
    }
}
