<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class Card extends Component
{
    public function __construct(
        public ?string $title = null,
        public bool $noPadding = false
    ) {}

    public function render(): View
    {
        return view('components.card');
    }
}
