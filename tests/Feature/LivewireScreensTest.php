<?php

use Illuminate\Support\Facades\File;
use Livewire\Livewire;
use MrFelipeMartins\Helix\Enums\ActivityLevel;
use MrFelipeMartins\Helix\Enums\ActivityType;
use MrFelipeMartins\Helix\Facades\Helix;
use MrFelipeMartins\Helix\Livewire\Activity;
use MrFelipeMartins\Helix\Livewire\Dashboard;
use MrFelipeMartins\Helix\Livewire\Indexes;
use MrFelipeMartins\Helix\Livewire\Show;
use MrFelipeMartins\Helix\Models\Index;
use MrFelipeMartins\Helix\Models\VectorActivity;

it('renders the dashboard screen', function () {
    Livewire::test(Dashboard::class)
        ->assertSee('Dashboard');
});

it('renders the indexes screen with existing indexes', function () {
    $root = config('helix.storage.index_root');
    $index = Index::query()->create([
        'name' => 'Primary Index',
        'dimension' => 128,
        'path' => "{$root}/primary-index",
    ]);
    File::ensureDirectoryExists($index->path());
    Helix::shouldReceive('stats')->andReturn(['storage' => []]);

    Livewire::test(Indexes::class)
        ->assertSee($index->name);
});

it('renders the activity screen with activity rows', function () {
    VectorActivity::query()->create([
        'index' => 'Primary Index',
        'type' => ActivityType::INSERT,
        'level' => ActivityLevel::INFO,
        'message' => 'Inserted record',
        'meta' => ['id' => 'record-1'],
        'created_at' => now(),
    ]);

    Livewire::test(Activity::class)
        ->assertSee('Inserted record');
});

it('renders the show screen for an index', function () {
    $root = config('helix.storage.index_root');
    $index = Index::query()->create([
        'name' => 'Show Index',
        'dimension' => 128,
        'path' => "{$root}/show-index",
    ]);
    File::ensureDirectoryExists($index->path());

    Livewire::test(Show::class, ['index' => $index])
        ->assertSee($index->name)
        ->assertSee('Records');
});
