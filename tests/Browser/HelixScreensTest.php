<?php

use Illuminate\Support\Facades\File;
use MrFelipeMartins\Helix\Facades\Helix;
use MrFelipeMartins\Helix\Models\Index;

it('renders the dashboard screen', function () {
    $page = visit(route('helix.dashboard'));

    $page->assertSee('Dashboard')
        ->assertSee('Create Index');
});

it('renders the indexes screen with data', function () {
    $root = config('helix.storage.index_root');
    $index = Index::query()->create([
        'name' => 'alpha-index',
        'dimension' => 128,
        'path' => "{$root}/alpha-index",
    ]);
    File::ensureDirectoryExists($index->path());
    Helix::shouldReceive('stats')->andReturn(['storage' => []]);

    $page = visit(route('helix.indexes'));

    $page->assertSee('Indexes')
        ->assertSee('Create Index')
        ->assertSee('Upload Snapshot')
        ->assertSee($index->name);
});

it('renders the activity screen', function () {
    $page = visit(route('helix.activity'));

    $page->assertSee('Activity & Logs')
        ->assertSee('Search logs...');
});

it('renders the index detail screen', function () {
    $root = config('helix.storage.index_root');
    $index = Index::query()->create([
        'name' => 'beta-index',
        'dimension' => 256,
        'path' => "{$root}/beta-index",
    ]);
    File::ensureDirectoryExists($index->path());

    $page = visit(route('helix.indexes.show', ['index' => $index->id]));

    $page->assertSee($index->name)
        ->assertSee('Optimize')
        ->assertSee('Records')
        ->assertSee('Info')
        ->assertSee('Snapshots')
        ->assertSee('Visualize');
});
