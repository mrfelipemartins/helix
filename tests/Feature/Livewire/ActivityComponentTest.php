<?php

use Livewire\Livewire;
use MrFelipeMartins\Helix\Enums\ActivityLevel;
use MrFelipeMartins\Helix\Enums\ActivityType;
use MrFelipeMartins\Helix\Livewire\Activity;
use MrFelipeMartins\Helix\Models\VectorActivity;

it('filters activity results by level, type, index, and search', function () {
    VectorActivity::query()->create([
        'index' => 'alpha',
        'type' => ActivityType::INSERT,
        'level' => ActivityLevel::INFO,
        'message' => 'Inserted record alpha',
        'meta' => ['id' => 'a1'],
        'created_at' => now()->subMinute(),
    ]);

    VectorActivity::query()->create([
        'index' => 'beta',
        'type' => ActivityType::DELETE,
        'level' => ActivityLevel::ERROR,
        'message' => 'Deleted record beta',
        'meta' => ['id' => 'b1'],
        'created_at' => now(),
    ]);

    Livewire::test(Activity::class)
        ->set('index', 'beta')
        ->set('level', 'error')
        ->set('type', 'delete')
        ->set('search', 'Deleted')
        ->assertSee('Deleted record beta')
        ->assertDontSee('Inserted record alpha');
});
