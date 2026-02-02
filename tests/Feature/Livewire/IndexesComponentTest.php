<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use MrFelipeMartins\Helix\Facades\Helix;
use MrFelipeMartins\Helix\Livewire\Indexes;
use MrFelipeMartins\Helix\Models\Index;

it('creates an index via the component', function () {
    Helix::shouldReceive('createIndex')
        ->once()
        ->withArgs(function (string $name, int $dimension) {
            return $name === 'New Index' && $dimension === 64;
        });
    Helix::shouldReceive('stats')->andReturn(['storage' => []]);

    Livewire::test(Indexes::class)
        ->set('form.name', 'New Index')
        ->set('form.dimension', 64)
        ->call('createIndex')
        ->assertSet('form.name', '')
        ->assertSet('showCreateModal', false);
});

it('validates create index input', function () {
    Livewire::test(Indexes::class)
        ->set('form.name', '')
        ->set('form.dimension', 0)
        ->call('createIndex')
        ->assertHasErrors(['form.name' => 'required', 'form.dimension' => 'min']);
});

it('uploads and restores a snapshot', function () {
    Storage::fake('private');

    Helix::shouldReceive('restoreSnapshot')
        ->once()
        ->withArgs(function (string $name, string $zipPath) {
            return $name === 'Restored Index' && str_contains($zipPath, 'helix/uploads/snapshot.zip');
        });

    Livewire::test(Indexes::class)
        ->set('uploadForm.name', 'Restored Index')
        ->set('uploadForm.file', UploadedFile::fake()->create('snapshot.zip', 1, 'application/zip'))
        ->call('uploadSnapshot')
        ->assertSet('showUploadModal', false);

    Storage::disk('private')->assertMissing('helix/uploads/snapshot.zip');
});

it('validates snapshot upload input', function () {
    Livewire::test(Indexes::class)
        ->set('uploadForm.name', '')
        ->call('uploadSnapshot')
        ->assertHasErrors(['uploadForm.name' => 'required', 'uploadForm.file' => 'required']);
});

it('deletes an index via the component', function () {
    $root = config('helix.storage.index_root');
    $index = Index::query()->create([
        'name' => 'Delete Index',
        'dimension' => 64,
        'path' => "{$root}/delete-index",
    ]);

    Helix::shouldReceive('drop')
        ->once()
        ->with($index);
    Helix::shouldReceive('stats')->andReturn(['storage' => []]);

    Livewire::test(Indexes::class)
        ->call('deleteIndex', $index);
});
