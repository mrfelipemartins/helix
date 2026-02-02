<?php

use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Livewire;
use MrFelipeMartins\Helix\Facades\Helix;
use MrFelipeMartins\Helix\Livewire\Index\Info;
use MrFelipeMartins\Helix\Livewire\Index\Records;
use MrFelipeMartins\Helix\Livewire\Index\Snapshots;
use MrFelipeMartins\Helix\Livewire\Index\Visualize;
use MrFelipeMartins\Helix\Models\Index;
use MrFelipeMartins\Helix\Models\Snapshot;

it('loads records from helix for the records tab', function () {
    $root = config('helix.storage.index_root');
    $index = Index::query()->create([
        'name' => 'Records Index',
        'dimension' => 128,
        'path' => "{$root}/records-index",
    ]);

    $paginator = new LengthAwarePaginator([
        ['id' => 'rec-1', 'vector' => [0.1, 0.2], 'metadata' => ['k' => 'v']],
    ], 1, 10, 1);

    Helix::shouldReceive('list')
        ->once()
        ->andReturn($paginator);

    Livewire::test(Records::class, ['index' => $index])
        ->assertSet('records', $paginator);
});

it('loads stats for the info tab', function () {
    $root = config('helix.storage.index_root');
    $index = Index::query()->create([
        'name' => 'Info Index',
        'dimension' => 128,
        'path' => "{$root}/info-index",
    ]);

    Helix::shouldReceive('stats')
        ->once()
        ->andReturn(['records' => ['vectors_total' => 12]]);

    Livewire::test(Info::class, ['index' => $index])
        ->assertSet('stats', ['records' => ['vectors_total' => 12]]);
});

it('creates and deletes snapshots from the snapshots tab', function () {
    $indexRoot = config('helix.storage.index_root');
    $snapshotRoot = config('helix.storage.snapshot_root');

    $index = Index::query()->create([
        'name' => 'Snapshots Index',
        'dimension' => 128,
        'path' => "{$indexRoot}/snapshots-index",
    ]);

    $snapshot = Snapshot::query()->create([
        'index_id' => $index->id,
        'name' => 'snap-1',
        'path' => storage_path("{$snapshotRoot}/snap-1.zip"),
        'size' => 10,
    ]);

    Helix::shouldReceive('createSnapshot')
        ->once()
        ->with(\Mockery::on(fn (Index $model) => $model->is($index)));

    Helix::shouldReceive('deleteSnapshot')
        ->once()
        ->with(\Mockery::on(fn (Snapshot $model) => $model->is($snapshot)));

    Livewire::test(Snapshots::class, ['index' => $index])
        ->call('createSnapshot')
        ->call('deleteSnapshot', $snapshot)
        ->assertSet('snapshots', Snapshot::query()->where('index_id', $index->id)->get());
});

it('normalizes points for the visualize tab', function () {
    $root = config('helix.storage.index_root');
    $index = Index::query()->create([
        'name' => 'Visual Index',
        'dimension' => 2,
        'path' => "{$root}/visual-index",
    ]);

    $paginator = new LengthAwarePaginator([
        ['id' => 'a', 'vector' => [0 => 0.0, 1 => 0.0], 'metadata' => null],
        ['id' => 'b', 'vector' => [0 => 10.0, 1 => 10.0], 'metadata' => ['tag' => 't']],
    ], 2, 200, 1);

    Helix::shouldReceive('list')
        ->once()
        ->andReturn($paginator);

    $component = Livewire::test(Visualize::class, ['index' => $index]);
    $points = $component->get('points');

    expect($points)->toHaveCount(2)
        ->and($points[0]['x'])->toBe(0.0)
        ->and($points[0]['y'])->toBe(0.0)
        ->and($points[1]['x'])->toBe(1.0)
        ->and($points[1]['y'])->toBe(1.0);
});
