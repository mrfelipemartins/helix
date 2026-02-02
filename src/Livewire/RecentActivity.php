<?php

namespace MrFelipeMartins\Helix\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Component;
use MrFelipeMartins\Helix\Models\VectorActivity;

#[Lazy]
class RecentActivity extends Component
{
    /**
     * @return Collection<int, VectorActivity>
     */
    #[Computed]
    public function activities(): Collection
    {
        /** @var Collection<int, VectorActivity> $activities */
        $activities = VectorActivity::query()->latest()->take(5)->get();

        return $activities;
    }

    public function placeholder(): View
    {
        return view('helix::components.placeholders.recent-activity');
    }

    public function render(): View
    {
        return view('helix::recent-activity');
    }
}
