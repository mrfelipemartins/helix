<?php

namespace MrFelipeMartins\Helix\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Dashboard extends Component
{
    #[Layout('helix::layouts.app')]
    public function render(): View
    {
        return view('helix::dashboard');
    }
}
