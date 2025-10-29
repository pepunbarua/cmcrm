<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class StatCard extends Component
{
    public function __construct(
        public string $title,
        public string $value,
        public ?string $change = null,
        public string $icon = 'chart',
        public string $color = 'purple'
    ) {}

    public function render(): View
    {
        return view('components.stat-card');
    }
}
