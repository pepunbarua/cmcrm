<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AuthCard extends Component
{
    public function render(): View
    {
        return view('components.auth-card');
    }
}
