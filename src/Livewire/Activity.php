<?php

namespace MrFelipeMartins\Helix\Livewire;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use MrFelipeMartins\Helix\Models\VectorActivity;

class Activity extends Component
{
    use WithPagination;

    #[Url(except: false)]
    public string $search = '';

    #[Url(except: false)]
    public string $level = '';

    #[Url(except: false)]
    public string $type = '';

    #[Url(except: false)]
    public string $index = '';

    public function updating(string $name, mixed $value): void
    {
        $this->resetPage();
    }

    /**
     * @return LengthAwarePaginator<array-key, VectorActivity>
     */
    public function getActivitiesProperty(): LengthAwarePaginator
    {
        return VectorActivity::query()
            ->when($this->index, fn ($q) => $q->where('index', $this->index))
            ->when($this->level, fn ($q) => $q->where('level', $this->level))
            ->when($this->type, fn ($q) => $q->where('type', $this->type))
            ->when($this->search, fn ($q) => $q->where('message', 'like', "%{$this->search}%"))
            ->latest('created_at')
            ->paginate(20);
    }

    #[Layout('helix::layouts.app')]
    public function render(): View
    {
        return view('helix::activity');
    }
}
