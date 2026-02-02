<?php

use Illuminate\Auth\GenericUser;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use MrFelipeMartins\Helix\Enums\IndexStatus;
use MrFelipeMartins\Helix\Helix;
use MrFelipeMartins\Helix\Managers\IndexManager;
use MrFelipeMartins\Helix\Models\Index;

function createIndexRecord(string $name = 'Core Index'): Index
{
    $slug = Str::slug($name);
    $root = config('helix.storage.index_root', 'helix-tests/indexes');

    return Index::query()->create([
        'name' => $name,
        'dimension' => 128,
        'path' => "{$root}/{$slug}",
        'status' => IndexStatus::READY->value,
    ]);
}

it('uses configured enable flag, path, and middleware', function () {
    $manager = \Mockery::mock(IndexManager::class);
    $helix = new Helix($manager);

    config([
        'helix.enabled' => false,
        'helix.path' => 'custom-helix',
        'helix.middleware' => ['web', 'auth'],
    ]);

    expect($helix->isEnabled())->toBeFalse()
        ->and($helix->path())->toBe('/custom-helix')
        ->and($helix->middleware())->toBe(['web', 'auth']);

    \Mockery::close();
});

it('checks gate permission in canView', function () {
    $manager = \Mockery::mock(IndexManager::class);
    $helix = new Helix($manager);

    Gate::define('viewHelix', fn ($user) => $user->id === 1);

    $allowed = new GenericUser(['id' => 1]);
    $denied = new GenericUser(['id' => 2]);

    expect($helix->canView($allowed))->toBeTrue()
        ->and($helix->canView($denied))->toBeFalse();

    \Mockery::close();
});

it('throws when resolving a missing index name', function () {
    $manager = \Mockery::mock(IndexManager::class);
    $helix = new Helix($manager);

    expect(fn () => $helix->stats('Missing Index'))
        ->toThrow(ModelNotFoundException::class);

    \Mockery::close();
});
