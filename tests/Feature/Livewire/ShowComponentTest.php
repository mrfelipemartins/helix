<?php

use Livewire\Livewire;
use MrFelipeMartins\Helix\Facades\Helix;
use MrFelipeMartins\Helix\Livewire\Show;
use MrFelipeMartins\Helix\Models\Index;

it('optimizes an index from the show component', function () {
    $root = config('helix.storage.index_root');
    $index = Index::query()->create([
        'name' => 'Optimize Index',
        'dimension' => 128,
        'path' => "{$root}/optimize-index",
    ]);

    Helix::shouldReceive('optimize')
        ->once()
        ->with(\Mockery::on(fn (Index $model) => $model->is($index)));
    Helix::shouldReceive('list')
        ->andReturn(new Illuminate\Pagination\LengthAwarePaginator([], 0, 200, 1));

    Livewire::test(Show::class, ['index' => $index])
        ->call('optimize');
});
