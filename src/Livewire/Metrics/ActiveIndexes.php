<?php

namespace MrFelipeMartins\Helix\Livewire\Metrics;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Component;
use MrFelipeMartins\Helix\Enums\IndexStatus;
use MrFelipeMartins\Helix\Models\Index;

#[Lazy]
class ActiveIndexes extends Component
{
    #[Computed]
    public function activeIndexes(): int
    {
        return Index::query()->where('status', IndexStatus::READY)->count();
    }

    public function placeholder(): View
    {
        return view('helix::components.placeholders.metrics');
    }

    public function render(): View
    {
        return view('helix::metrics.active-indexes');
    }
}
