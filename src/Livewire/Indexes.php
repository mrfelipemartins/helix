<?php

namespace MrFelipeMartins\Helix\Livewire;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use MrFelipeMartins\Helix\Facades\Helix;
use MrFelipeMartins\Helix\Models\Index;

class Indexes extends Component
{
    use WithFileUploads;
    use WithPagination;

    #[Url(except: false)]
    public bool $showCreateModal = false;

    public bool $showUploadModal = false;

    /** @var array{name: string, dimension: int} */
    public array $form = [
        'name' => '',
        'dimension' => 1536,
    ];

    /** @var array{name: string, file: UploadedFile|null} */
    public array $uploadForm = [
        'name' => '',
        'file' => null,
    ];

    public string $search = '';

    public function createIndex(): void
    {
        $data = $this->validate([
            'form.name' => 'required|string|max:255',
            'form.dimension' => 'required|integer|min:1',
        ]);

        Helix::createIndex(
            name: $data['form']['name'],
            dimension: $data['form']['dimension'],
        );

        $this->reset('form', 'showCreateModal');

        unset($this->indexes);
    }

    public function uploadSnapshot(): void
    {
        $data = $this->validate([
            'uploadForm.name' => 'required|string|max:255',
            'uploadForm.file' => 'required|file|mimes:zip',
        ]);

        $file = $data['uploadForm']['file'];
        $stored = $file->storeAs('helix/uploads', $file->getClientOriginalName());

        Helix::restoreSnapshot(
            name: $data['uploadForm']['name'],
            zipPath: storage_path("app/private/{$stored}"),
        );

        Storage::disk('private')->delete($stored);

        $this->reset('uploadForm', 'showUploadModal');

        unset($this->indexes);
    }

    public function deleteIndex(Index $index): void
    {
        Helix::drop($index);

        unset($this->indexes);
    }

    /**
     * @return LengthAwarePaginator<int, Index>
     */
    #[Computed]
    public function indexes(): LengthAwarePaginator
    {
        return Index::query()
            ->latest()
            ->when($this->search, fn (Builder $query) => $query->where('name', 'like', "%{$this->search}%"))
            ->paginate(20);
    }

    #[Layout('helix::layouts.app')]
    public function render(): View
    {
        return view('helix::indexes');
    }
}
