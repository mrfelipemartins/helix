<?php

namespace MrFelipeMartins\Helix\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use MrFelipeMartins\Helix\Facades\Helix;
use MrFelipeMartins\Helix\Models\Index;

class Show extends Component
{
    public Index $index;

    /** @var array<int, array{key: string, label: string}> */
    public array $tabs = [
        ['key' => 'records', 'label' => 'Records'],
        ['key' => 'info', 'label' => 'Info'],
        ['key' => 'snapshots', 'label' => 'Snapshots'],
        ['key' => 'visualize', 'label' => 'Visualize'],
    ];

    public function optimize(): void
    {
        Helix::optimize($this->index);

        $this->js('alert("Index optimized successfully!")');
    }

    public function mount(Index $index): void
    {
        $this->index = $index;
    }

    #[Layout('helix::layouts.app')]
    public function render(): View
    {
        return view('helix::show');
    }
}
