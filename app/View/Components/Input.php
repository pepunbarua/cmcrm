<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class Input extends Component
{
    public function __construct(
        public string $name,
        public string $type = 'text',
        public ?string $placeholder = null,
        public ?string $value = null,
        public ?string $label = null
    ) {}

    public function render(): View
    {
        return view('components.input');
    }
}
