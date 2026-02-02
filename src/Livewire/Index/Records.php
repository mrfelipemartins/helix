<?php

namespace MrFelipeMartins\Helix\Livewire\Index;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Component;
use Livewire\WithPagination;
use MrFelipeMartins\Helix\Facades\Helix;
use MrFelipeMartins\Helix\Models\Index;

#[Lazy]
class Records extends Component
{
    use WithPagination;

    public Index $index;

    /**
     * @return LengthAwarePaginator<int, array{id: string, vector: array<int,float>, metadata?: mixed}>
     */
    #[Computed]
    public function records(): LengthAwarePaginator
    {
        return Helix::list($this->index, page: $this->getPage(), perPage: 10);
    }

    public function placeholder(): View
    {
        return view('helix::components.placeholders.tabs');
    }

    public function render(): View
    {
        return view('helix::index.records');
    }
}
