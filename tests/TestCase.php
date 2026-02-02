<?php

namespace MrFelipeMartins\Helix\Tests;

use Illuminate\Support\Facades\File;
use Livewire\LivewireServiceProvider;
use MrFelipeMartins\Helix\HelixServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
            HelixServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('helix.enabled', true);
        $app['config']->set('helix.gate', env('HELIX_GATE', false));
        $app['config']->set('helix.storage.index_disk', 'local');
        $app['config']->set('helix.storage.index_root', $this->helixTestRoot().'/indexes');
        $app['config']->set('helix.storage.snapshot_disk', 'local');
        $app['config']->set('helix.storage.snapshot_root', $this->helixTestRoot().'/snapshots');

        $app['config']->set('filesystems.default', 'private');
        $app['config']->set('filesystems.disks.private', [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'throw' => false,
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->artisan('migrate')->run();
    }

    protected function tearDown(): void
    {
        File::deleteDirectory(storage_path($this->helixTestRoot()));

        parent::tearDown();
    }

    protected function helixTestRoot(): string
    {
        $token = $_SERVER['TEST_TOKEN'] ?? null;

        return $token
            ? "helix-tests/{$token}"
            : 'helix-tests';
    }
}
