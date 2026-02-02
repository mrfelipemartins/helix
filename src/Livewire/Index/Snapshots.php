<?php

namespace MrFelipeMartins\Helix\Livewire\Index;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Component;
use MrFelipeMartins\Helix\Facades\Helix;
use MrFelipeMartins\Helix\Models\Index;
use MrFelipeMartins\Helix\Models\Snapshot;

#[Lazy]
class Snapshots extends Component
{
    public Index $index;

    /**
     * @return Collection<int, Snapshot>
     */
    #[Computed]
    public function snapshots(): Collection
    {
        return Snapshot::query()
            ->where('index_id', $this->index->id)
            ->latest()
            ->get();
    }

    public function createSnapshot(): void
    {
        Helix::createSnapshot($this->index);
    }

    public function deleteSnapshot(Snapshot $snapshot): void
    {
        Helix::deleteSnapshot($snapshot);
    }

    public function placeholder(): View
    {
        return view('helix::components.placeholders.tabs');
    }

    public function render(): View
    {
        return view('helix::index.snapshots');
    }
}
