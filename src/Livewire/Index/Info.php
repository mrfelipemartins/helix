<?php

namespace MrFelipeMartins\Helix\Livewire\Index;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Component;
use Livewire\WithPagination;
use MrFelipeMartins\Helix\Facades\Helix;
use MrFelipeMartins\Helix\Models\Index;

#[Lazy]
class Info extends Component
{
    use WithPagination;

    public Index $index;

    /**
     * @return array<string, mixed>
     */
    #[Computed]
    public function stats(): array
    {
        return Helix::stats($this->index);
    }

    public function placeholder(): View
    {
        return view('helix::components.placeholders.tabs');
    }

    public function render(): View
    {
        return view('helix::index.info');
    }
}
