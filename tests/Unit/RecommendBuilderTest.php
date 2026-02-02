<?php

use MrFelipeMartins\Helix\Managers\IndexManager;
use MrFelipeMartins\Helix\Models\Index;
use MrFelipeMartins\Helix\Search\RecommendBuilder;

afterEach(function () {
    \Mockery::close();
});

it('builds a recommendation query from ids and executes search', function () {
    $root = config('helix.storage.index_root');
    $index = Index::query()->create([
        'name' => 'Recommend Index',
        'dimension' => 2,
        'path' => "{$root}/recommend-index",
    ]);

    $manager = \Mockery::mock(IndexManager::class);
    $manager->shouldReceive('vector')
        ->once()
        ->withArgs(fn (Index $store, string $id) => $store->is($index) && $id === 'pos')
        ->andReturn([1.0, 0.0]);
    $manager->shouldReceive('vector')
        ->once()
        ->withArgs(fn (Index $store, string $id) => $store->is($index) && $id === 'neg')
        ->andReturn([0.0, 1.0]);
    $manager->shouldReceive('search')
        ->once()
        ->withArgs(function (Index $store, array $vector, int $limit) use ($index) {
            return $store->is($index) && $vector === [2.0, -1.0] && $limit >= 1;
        })
        ->andReturn([['id' => 'x', 'score' => 0.9]]);

    $builder = new RecommendBuilder($manager);

    $results = $builder
        ->on($index)
        ->positive('pos')
        ->negative('neg')
        ->limit(1)
        ->get();

    expect($results)->toBe([['id' => 'x', 'score' => 0.9]]);
});

it('builds a recommendation query from vectors', function () {
    $root = config('helix.storage.index_root');
    $index = Index::query()->create([
        'name' => 'Recommend Vectors',
        'dimension' => 2,
        'path' => "{$root}/recommend-vectors",
    ]);

    $manager = \Mockery::mock(IndexManager::class);
    $manager->shouldReceive('search')
        ->once()
        ->withArgs(function (Index $store, array $vector, int $limit) use ($index) {
            return $store->is($index) && $vector === [1.0, 1.0] && $limit >= 1;
        })
        ->andReturn([['id' => 'y', 'score' => 0.8]]);

    $builder = new RecommendBuilder($manager);

    $results = $builder
        ->on($index)
        ->positiveVectors([
            [0.0, 1.0],
            [2.0, 1.0],
        ])
        ->get();

    expect($results)->toBe([['id' => 'y', 'score' => 0.8]]);
});

it('requires at least one positive example', function () {
    $root = config('helix.storage.index_root');
    $index = Index::query()->create([
        'name' => 'Recommend Empty',
        'dimension' => 2,
        'path' => "{$root}/recommend-empty",
    ]);

    $manager = \Mockery::mock(IndexManager::class);

    $builder = new RecommendBuilder($manager);

    expect(fn () => $builder->on($index)->negative('neg')->get())
        ->toThrow(InvalidArgumentException::class);
});
