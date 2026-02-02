<?php

use Illuminate\Support\Facades\Route;
use MrFelipeMartins\Helix\Http\Controllers\SnapshotDownloadController;
use MrFelipeMartins\Helix\Livewire\Activity;
use MrFelipeMartins\Helix\Livewire\Dashboard;
use MrFelipeMartins\Helix\Livewire\Indexes;
use MrFelipeMartins\Helix\Livewire\Show;

$middleware = config('helix.middleware', ['web']);

if (config('helix.gate')) {
    $middleware[] = 'can:viewHelix';
}

Route::prefix(config('helix.path'))
    ->middleware($middleware)
    ->group(function () {
        Route::get('/', Dashboard::class)->name('helix.dashboard');
        Route::get('/indexes', Indexes::class)->name('helix.indexes');
        Route::get('/activity', Activity::class)->name('helix.activity');
        Route::get('/show/{index}', Show::class)->name('helix.indexes.show');
        Route::get('/snapshots/{snapshot}/download', SnapshotDownloadController::class)->name('helix.snapshots.download');
    });
