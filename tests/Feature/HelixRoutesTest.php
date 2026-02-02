<?php

use Illuminate\Auth\GenericUser;
use Illuminate\Support\Facades\Gate;

afterEach(function () {
    unset($_ENV['HELIX_GATE']);
});

it('allows access when gate is disabled', function () {
    $this->get(route('helix.dashboard'))
        ->assertOk();
});

it('denies access when gate is enabled and fails', function () {
    $_ENV['HELIX_GATE'] = 'true';
    $this->refreshApplication();
    $this->artisan('migrate')->run();

    Gate::define('viewHelix', fn ($user = null) => false);
    $this->actingAs(new GenericUser(['id' => 1]));

    $this->get(route('helix.dashboard'))
        ->assertForbidden();
});

it('allows access when gate is enabled and passes', function () {
    $_ENV['HELIX_GATE'] = 'true';
    $this->refreshApplication();
    $this->artisan('migrate')->run();

    Gate::define('viewHelix', fn ($user = null) => true);
    $this->actingAs(new GenericUser(['id' => 1]));

    $this->get(route('helix.dashboard'))
        ->assertOk();
});
