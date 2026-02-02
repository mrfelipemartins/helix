<?php

use Livewire\Livewire;
use MrFelipeMartins\Helix\Enums\ActivityLevel;
use MrFelipeMartins\Helix\Enums\ActivityType;
use MrFelipeMartins\Helix\Livewire\RecentActivity;
use MrFelipeMartins\Helix\Models\VectorActivity;

it('returns the five most recent activities', function () {
    foreach (range(1, 6) as $i) {
        VectorActivity::query()->create([
            'index' => 'index-'.$i,
            'type' => ActivityType::INSERT,
            'level' => ActivityLevel::INFO,
            'message' => 'Activity '.$i,
            'meta' => ['id' => $i],
            'created_at' => now()->addSeconds($i),
        ]);
    }

    $component = Livewire::test(RecentActivity::class);
    $activities = $component->get('activities');

    expect($activities)->toHaveCount(5)
        ->and($activities->first()->message)->toBe('Activity 6')
        ->and($activities->last()->message)->toBe('Activity 2');
});
