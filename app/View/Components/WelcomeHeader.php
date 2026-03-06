<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class WelcomeHeader extends Component
{
    public $name;
    public $role;

    public function __construct($name, $role)
    {
        $this->name = $name;
        $this->role = $role;
    }

    public function render()
    {
        return view('components.welcome-header');
    }
}