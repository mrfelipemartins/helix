<?php

namespace MrFelipeMartins\Helix\Livewire\Metrics;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Component;
use MrFelipeMartins\Helix\Models\Index;

#[Lazy]
class TotalIndexes extends Component
{
    #[Computed]
    public function totalIndexes(): int
    {
        return Index::query()->count();
    }

    public function placeholder(): View
    {
        return view('helix::components.placeholders.metrics');
    }

    public function render(): View
    {
        return view('helix::metrics.total-indexes');
    }
}
