<?php

use Illuminate\Support\Facades\File;
use MrFelipeMartins\Helix\Models\Snapshot;

it('returns a snapshot download response when the file exists', function () {
    config(['helix.gate' => false]);

    $snapshotRoot = config('helix.storage.snapshot_root');
    $path = storage_path("{$snapshotRoot}/sample.zip");
    File::ensureDirectoryExists(dirname($path));
    File::put($path, 'snapshot');

    $snapshot = Snapshot::query()->create([
        'index_id' => 1,
        'name' => 'sample',
        'path' => $path,
        'size' => 8,
    ]);

    $this->get(route('helix.snapshots.download', $snapshot))
        ->assertOk()
        ->assertHeader('content-disposition');
});

it('returns 404 when the snapshot file is missing', function () {
    config(['helix.gate' => false]);
    $snapshotRoot = config('helix.storage.snapshot_root');

    $snapshot = Snapshot::query()->create([
        'index_id' => 1,
        'name' => 'missing',
        'path' => storage_path("{$snapshotRoot}/missing.zip"),
        'size' => 0,
    ]);

    $this->get(route('helix.snapshots.download', $snapshot))
        ->assertNotFound();
});
