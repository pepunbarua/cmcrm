<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class Button extends Component
{
    public function __construct(
        public string $type = 'submit',
        public string $variant = 'primary'
    ) {}

    public function render(): View
    {
        return view('components.button');
    }
}
