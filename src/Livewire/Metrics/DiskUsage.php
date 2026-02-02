<?php

namespace MrFelipeMartins\Helix\Livewire\Metrics;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Component;
use MrFelipeMartins\Helix\Managers\IndexManager;
use MrFelipeMartins\Helix\Models\Index;
use MrFelipeMartins\Helix\Support\Helpers;

#[Lazy]
class DiskUsage extends Component
{
    #[Computed]
    public function diskUsage(): string
    {
        $manager = app(IndexManager::class);

        $bytes = Index::all()->reduce(function ($carry, Index $store) use ($manager) {
            $stats = $manager->stats($store);
            $storage = is_array($stats['storage'] ?? null) ? $stats['storage'] : [];

            return $carry
                + ($storage['vector_file_bytes'] ?? 0)
                + ($storage['graph_file_bytes'] ?? 0)
                + ($storage['meta_file_bytes'] ?? 0);
        }, 0);

        return Helpers::formatBytes($bytes);
    }

    public function placeholder(): View
    {
        return view('helix::components.placeholders.metrics');
    }

    public function render(): View
    {
        return view('helix::metrics.disk-usage');
    }
}
