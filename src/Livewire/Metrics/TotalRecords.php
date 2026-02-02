<?php

namespace MrFelipeMartins\Helix\Livewire\Metrics;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Component;
use MrFelipeMartins\Helix\Managers\IndexManager;
use MrFelipeMartins\Helix\Models\Index;

#[Lazy]
class TotalRecords extends Component
{
    #[Computed]
    public function totalRecords(): int
    {
        $manager = app(IndexManager::class);

        return Index::all()->reduce(function ($carry, Index $store) use ($manager) {
            $stats = $manager->stats($store);
            $records = is_array($stats['records'] ?? null) ? $stats['records'] : [];

            return $carry + ($records['vectors_total'] ?? 0);
        }, 0);
    }

    public function placeholder(): View
    {
        return view('helix::components.placeholders.metrics');
    }

    public function render(): View
    {
        return view('helix::metrics.total-records');
    }
}
