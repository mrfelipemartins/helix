<?php

use MrFelipeMartins\Helix\Managers\IndexManager;
use MrFelipeMartins\Helix\Models\Index;
use MrFelipeMartins\Helix\Search\SearchBuilder;

afterEach(function () {
    \Mockery::close();
});

it('filters results by metadata and score threshold', function () {
    $root = config('helix.storage.index_root');
    $index = Index::query()->create([
        'name' => 'Search Index',
        'dimension' => 128,
        'path' => "{$root}/search-index",
    ]);

    $results = [
        ['id' => '1', 'score' => 0.91, 'metadata' => ['document_id' => 1, 'status' => 'active']],
        ['id' => '2', 'score' => 0.45, 'metadata' => ['document_id' => 2, 'status' => 'active']],
        ['id' => '3', 'score' => 0.88, 'metadata' => ['document_id' => 1, 'status' => 'archived']],
        ['id' => '4', 'score' => 0.95, 'metadata' => ['document_id' => 1, 'status' => 'active']],
    ];

    $manager = \Mockery::mock(IndexManager::class);
    $manager->shouldReceive('search')
        ->once()
        ->withArgs(function (Index $store, array $vector, int $limit) use ($index) {
            return $store->is($index) && $vector === [0.1, 0.2] && $limit >= 2;
        })
        ->andReturn($results);

    $builder = new SearchBuilder($manager);

    $filtered = $builder
        ->on($index)
        ->query([0.1, 0.2])
        ->where('document_id', 1)
        ->where('status', 'active')
        ->scoreThreshold(0.9)
        ->get();

    expect(array_column($filtered, 'id'))->toBe(['1', '4']);
});

it('supports between and like metadata filters', function () {
    $root = config('helix.storage.index_root');
    $index = Index::query()->create([
        'name' => 'Filter Index',
        'dimension' => 128,
        'path' => "{$root}/filter-index",
    ]);

    $results = [
        ['id' => 'a', 'score' => 0.5, 'metadata' => ['title' => 'Alpha Report', 'metrics' => ['views' => 5]]],
        ['id' => 'b', 'score' => 0.6, 'metadata' => ['title' => 'Beta Report', 'metrics' => ['views' => 15]]],
        ['id' => 'c', 'score' => 0.7, 'metadata' => ['title' => 'Gamma Notes', 'metrics' => ['views' => 25]]],
    ];

    $manager = \Mockery::mock(IndexManager::class);
    $manager->shouldReceive('search')
        ->once()
        ->andReturn($results);

    $builder = new SearchBuilder($manager);

    $filtered = $builder
        ->on($index)
        ->query([0.3, 0.4])
        ->where('title', 'like', '%Report')
        ->whereBetween('metrics.views', [10, 20])
        ->get();

    expect(array_column($filtered, 'id'))->toBe(['b']);
});
