<?php

namespace MrFelipeMartins\Helix;

use Composer\InstalledVersions;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\View\Compilers\BladeCompiler;
use Livewire\LivewireManager;
use MrFelipeMartins\Helix\Console\InstallCommand;
use MrFelipeMartins\Helix\Console\OptimizeIndexesCommand;
use MrFelipeMartins\Helix\Console\PruneActivitiesCommand;
use MrFelipeMartins\Helix\Engines\Contracts\VectorEngine;
use MrFelipeMartins\Helix\Engines\VektorEngine;
use MrFelipeMartins\Helix\Events\IndexCreated;
use MrFelipeMartins\Helix\Events\IndexDropped;
use MrFelipeMartins\Helix\Events\RecordDeleted;
use MrFelipeMartins\Helix\Events\RecordInserted;
use MrFelipeMartins\Helix\Events\VectorSearchPerformed;
use MrFelipeMartins\Helix\Listeners\RecordActivity;
use MrFelipeMartins\Helix\Managers\IndexManager;

class HelixServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/helix.php',
            'helix'
        );

        $this->app->singleton(Helix::class);
        $this->app->singleton(VectorEngine::class, VektorEngine::class);
        $this->app->singleton(IndexManager::class, fn ($app) => new IndexManager(
            $app->make(VectorEngine::class),
        ));
    }

    public function boot(): void
    {
        $this->registerRoutes();
        $this->registerComponents();
        $this->registerResources();
        $this->registerPublishing();
        $this->registerCommands();
        $this->registerGate();
        $this->registerActivityListeners();
    }

    protected function registerRoutes(): void
    {
        if (! config('helix.enabled')) {
            return;
        }

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
    }

    protected function registerComponents(): void
    {
        $this->callAfterResolving('blade.compiler', function (BladeCompiler $blade) {
            $blade->anonymousComponentPath(__DIR__.'/../resources/views/components', 'helix');
        });

        $this->callAfterResolving('livewire', function (LivewireManager $livewire, Application $app) {
            $configMiddleware = (array) config('helix.middleware', []);
            $middleware = [];

            foreach ($configMiddleware as $item) {
                $middleware[] = is_string($item) ? Str::before($item, ':') : $item;
            }

            $livewire->component('helix.dashboard', Livewire\Dashboard::class);
            $livewire->component('helix.indexes', Livewire\Indexes::class);
            $livewire->component('helix.activity', Livewire\Activity::class);
            $livewire->component('helix.show', Livewire\Show::class);
            $livewire->component('helix.metrics.total-indexes', Livewire\Metrics\TotalIndexes::class);
            $livewire->component('helix.metrics.total-records', Livewire\Metrics\TotalRecords::class);
            $livewire->component('helix.metrics.active-indexes', Livewire\Metrics\ActiveIndexes::class);
            $livewire->component('helix.metrics.disk-usage', Livewire\Metrics\DiskUsage::class);
            $livewire->component('helix.recent-activity', Livewire\RecentActivity::class);
            $livewire->component('helix.index.records', Livewire\Index\Records::class);
            $livewire->component('helix.index.info', Livewire\Index\Info::class);
            $livewire->component('helix.index.snapshots', Livewire\Index\Snapshots::class);
            $livewire->component('helix.index.visualize', Livewire\Index\Visualize::class);

            $livewire->addPersistentMiddleware($middleware);
        });
    }

    protected function registerResources(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'helix');
    }

    protected function registerPublishing(): void
    {
        $this->publishes([
            __DIR__.'/../config/helix.php' => config_path('helix.php'),
        ], ['helix', 'helix-config']);

        $method = method_exists($this, 'publishesMigrations') ? 'publishesMigrations' : 'publishes';

        $this->{$method}([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], ['helix', 'helix-migrations']);

    }

    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                OptimizeIndexesCommand::class,
                PruneActivitiesCommand::class,
            ]);

            AboutCommand::add('Helix', fn () => [
                'Version' => InstalledVersions::getPrettyVersion('mrfelipemartins/helix'),
                'Enabled' => AboutCommand::format(config('helix.enabled'), console: fn ($value) => $value ? '<fg=yellow;options=bold>ENABLED</>' : 'OFF'),
            ]);
        }
    }

    protected function registerGate(): void
    {
        $this->callAfterResolving(Gate::class, function (Gate $gate, Application $app) {
            $gate->define('viewHelix', fn ($user = null) => $app->environment('local'));
        });
    }

    protected function registerActivityListeners(): void
    {
        if (! config('helix.activity.enabled')) {
            return;
        }

        Event::listen([
            IndexCreated::class,
            IndexDropped::class,
            RecordInserted::class,
            RecordDeleted::class,
            VectorSearchPerformed::class,
        ], RecordActivity::class);
    }
}
