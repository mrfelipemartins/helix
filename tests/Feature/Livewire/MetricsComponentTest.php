<?php

use Livewire\Livewire;
use MrFelipeMartins\Helix\Enums\IndexStatus;
use MrFelipeMartins\Helix\Livewire\Metrics\ActiveIndexes;
use MrFelipeMartins\Helix\Livewire\Metrics\DiskUsage;
use MrFelipeMartins\Helix\Livewire\Metrics\TotalIndexes;
use MrFelipeMartins\Helix\Livewire\Metrics\TotalRecords;
use MrFelipeMartins\Helix\Managers\IndexManager;
use MrFelipeMartins\Helix\Models\Index;

beforeEach(function () {
    $root = config('helix.storage.index_root');

    Index::query()->create([
        'name' => 'Index A',
        'dimension' => 128,
        'path' => "{$root}/index-a",
        'status' => IndexStatus::READY->value,
    ]);

    Index::query()->create([
        'name' => 'Index B',
        'dimension' => 128,
        'path' => "{$root}/index-b",
        'status' => IndexStatus::ERROR->value,
    ]);
});

afterEach(function () {
    \Mockery::close();
});

it('calculates total indexes', function () {
    $component = Livewire::test(TotalIndexes::class);

    expect($component->get('totalIndexes'))->toBe(2);
});

it('calculates active indexes', function () {
    $component = Livewire::test(ActiveIndexes::class);

    expect($component->get('activeIndexes'))->toBe(1);
});

it('calculates total records across indexes', function () {
    $manager = \Mockery::mock(IndexManager::class);

    $manager->shouldReceive('stats')
        ->twice()
        ->andReturnUsing(function (Index $index) {
            return $index->name === 'Index A'
                ? ['records' => ['vectors_total' => 5]]
                : ['records' => ['vectors_total' => 12]];
        });

    app()->instance(IndexManager::class, $manager);

    $component = Livewire::test(TotalRecords::class);

    expect($component->get('totalRecords'))->toBe(17);
});

it('calculates disk usage across indexes', function () {
    $manager = \Mockery::mock(IndexManager::class);

    $manager->shouldReceive('stats')
        ->twice()
        ->andReturnUsing(function (Index $index) {
            return $index->name === 'Index A'
                ? ['storage' => ['vector_file_bytes' => 1024, 'graph_file_bytes' => 0, 'meta_file_bytes' => 0]]
                : ['storage' => ['vector_file_bytes' => 2048, 'graph_file_bytes' => 0, 'meta_file_bytes' => 0]];
        });

    app()->instance(IndexManager::class, $manager);

    $component = Livewire::test(DiskUsage::class);

    expect($component->get('diskUsage'))->toBe('3.0 KB');
});
