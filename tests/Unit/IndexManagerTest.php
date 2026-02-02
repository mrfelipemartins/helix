<?php

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use MrFelipeMartins\Helix\Engines\Contracts\VectorEngine;
use MrFelipeMartins\Helix\Enums\IndexStatus;
use MrFelipeMartins\Helix\Events\IndexCreated;
use MrFelipeMartins\Helix\Events\IndexDropped;
use MrFelipeMartins\Helix\Events\RecordDeleted;
use MrFelipeMartins\Helix\Events\RecordInserted;
use MrFelipeMartins\Helix\Events\VectorSearchPerformed;
use MrFelipeMartins\Helix\Managers\IndexManager;
use MrFelipeMartins\Helix\Models\Index;
use MrFelipeMartins\Helix\Models\Snapshot;

beforeEach(function () {
    $this->engine = \Mockery::mock(VectorEngine::class);
    $this->manager = new IndexManager($this->engine);
});

afterEach(function () {
    \Mockery::close();
});

function makeIndex(string $name = 'Test Index', ?string $path = null): Index
{
    $slug = Str::slug($name);
    $root = config('helix.storage.index_root');

    return Index::query()->create([
        'name' => $name,
        'dimension' => 128,
        'path' => $path ?? "{$root}/{$slug}",
        'status' => IndexStatus::READY->value,
    ]);
}

it('creates an index folder, persists the index, and dispatches an event', function () {
    Event::fake([IndexCreated::class]);

    $this->engine->shouldReceive('create')
        ->once()
        ->with(\Mockery::type(Index::class));

    $index = $this->manager->create('My Index', 256);

    expect($index->name)->toBe('My Index')
        ->and($index->dimension)->toBe(256)
        ->and($index->status)->toBe(IndexStatus::READY)
        ->and(File::isDirectory($index->path()))->toBeTrue();

    Event::assertDispatched(IndexCreated::class, function ($event) use ($index) {
        return $event->store->is($index);
    });
});

it('marks index as ready and dispatches event on successful insert', function () {
    Event::fake([RecordInserted::class]);

    $index = makeIndex();

    $this->engine->shouldReceive('insert')
        ->once()
        ->with($index, 'record-1', [0.1, 0.2], ['source' => 'test']);

    $this->manager->insert($index, 'record-1', [0.1, 0.2], ['source' => 'test']);

    $index->refresh();

    expect($index->status)->toBe(IndexStatus::READY);

    Event::assertDispatched(RecordInserted::class, function ($event) use ($index) {
        return $event->store->is($index) && $event->id === 'record-1';
    });
});

it('marks index as error when insert fails', function () {
    $index = makeIndex();

    $this->engine->shouldReceive('insert')
        ->once()
        ->andThrow(new RuntimeException('boom'));

    expect(fn () => $this->manager->insert($index, 'record-1', [1.0]))
        ->toThrow(RuntimeException::class);

    $index->refresh();

    expect($index->status)->toBe(IndexStatus::ERROR);
});

it('marks index as ready and dispatches event on successful search', function () {
    Event::fake([VectorSearchPerformed::class]);

    $index = makeIndex();

    $results = [
        ['id' => 'a', 'score' => 0.98],
    ];

    $this->engine->shouldReceive('search')
        ->once()
        ->with($index, [0.2, 0.3], 5)
        ->andReturn($results);

    $response = $this->manager->search($index, [0.2, 0.3], 5);

    $index->refresh();

    expect($response)->toBe($results)
        ->and($index->status)->toBe(IndexStatus::READY);

    Event::assertDispatched(VectorSearchPerformed::class, function ($event) use ($index) {
        return $event->store->is($index) && $event->limit === 5;
    });
});

it('retrieves a stored vector by id', function () {
    $index = makeIndex();

    $this->engine->shouldReceive('vector')
        ->once()
        ->with($index, 'vec-1')
        ->andReturn([0.1, 0.2]);

    $vector = $this->manager->vector($index, 'vec-1');

    expect($vector)->toBe([0.1, 0.2]);
});

it('marks index as error when search fails', function () {
    $index = makeIndex();

    $this->engine->shouldReceive('search')
        ->once()
        ->andThrow(new RuntimeException('boom'));

    expect(fn () => $this->manager->search($index, [0.2]))
        ->toThrow(RuntimeException::class);

    $index->refresh();

    expect($index->status)->toBe(IndexStatus::ERROR);
});

it('marks index as ready and returns paginator on list', function () {
    $index = makeIndex();

    $paginator = new LengthAwarePaginator([
        ['id' => '1', 'vector' => [0.1, 0.2]],
    ], 1, 50, 1);

    $this->engine->shouldReceive('list')
        ->once()
        ->with($index, 1, 50)
        ->andReturn($paginator);

    $response = $this->manager->list($index);

    $index->refresh();

    expect($response)->toBe($paginator)
        ->and($index->status)->toBe(IndexStatus::READY);
});

it('marks index as error when list fails', function () {
    $index = makeIndex();

    $this->engine->shouldReceive('list')
        ->once()
        ->andThrow(new RuntimeException('boom'));

    expect(fn () => $this->manager->list($index))
        ->toThrow(RuntimeException::class);

    $index->refresh();

    expect($index->status)->toBe(IndexStatus::ERROR);
});

it('marks index as ready and dispatches event on successful delete', function () {
    Event::fake([RecordDeleted::class]);

    $index = makeIndex();

    $this->engine->shouldReceive('delete')
        ->once()
        ->with($index, 'record-2');

    $this->manager->delete($index, 'record-2');

    $index->refresh();

    expect($index->status)->toBe(IndexStatus::READY);

    Event::assertDispatched(RecordDeleted::class, function ($event) use ($index) {
        return $event->store->is($index) && $event->id === 'record-2';
    });
});

it('marks index as error when delete fails', function () {
    $index = makeIndex();

    $this->engine->shouldReceive('delete')
        ->once()
        ->andThrow(new RuntimeException('boom'));

    expect(fn () => $this->manager->delete($index, 'record-2'))
        ->toThrow(RuntimeException::class);

    $index->refresh();

    expect($index->status)->toBe(IndexStatus::ERROR);
});

it('marks index as ready and returns stats', function () {
    $index = makeIndex();

    $stats = ['storage' => ['vector_file_bytes' => 12]];

    $this->engine->shouldReceive('stats')
        ->once()
        ->with($index)
        ->andReturn($stats);

    $response = $this->manager->stats($index);

    $index->refresh();

    expect($response)->toBe($stats)
        ->and($index->status)->toBe(IndexStatus::READY);
});

it('marks index as error when stats fails', function () {
    $index = makeIndex();

    $this->engine->shouldReceive('stats')
        ->once()
        ->andThrow(new RuntimeException('boom'));

    expect(fn () => $this->manager->stats($index))
        ->toThrow(RuntimeException::class);

    $index->refresh();

    expect($index->status)->toBe(IndexStatus::ERROR);
});

it('optimizes index and returns to ready status', function () {
    $index = makeIndex();

    $this->engine->shouldReceive('optimize')
        ->once()
        ->with($index);

    $this->manager->optimize($index);

    $index->refresh();

    expect($index->status)->toBe(IndexStatus::READY);
});

it('marks index as error when optimize fails', function () {
    $index = makeIndex();

    $this->engine->shouldReceive('optimize')
        ->once()
        ->andThrow(new RuntimeException('boom'));

    expect(fn () => $this->manager->optimize($index))
        ->toThrow(RuntimeException::class);

    $index->refresh();

    expect($index->status)->toBe(IndexStatus::ERROR);
});

it('drops index, dispatches event, and deletes record', function () {
    Event::fake([IndexDropped::class]);

    $index = makeIndex();

    $this->engine->shouldReceive('drop')
        ->once()
        ->with($index);

    $this->manager->drop($index);

    expect(Index::query()->find($index->id))->toBeNull();

    Event::assertDispatched(IndexDropped::class, function ($event) use ($index) {
        return $event->store->id === $index->id;
    });
});

it('marks index as error when drop fails', function () {
    $index = makeIndex();

    $this->engine->shouldReceive('drop')
        ->once()
        ->andThrow(new RuntimeException('boom'));

    expect(fn () => $this->manager->drop($index))
        ->toThrow(RuntimeException::class);

    $index->refresh();

    expect($index->status)->toBe(IndexStatus::ERROR);
});

it('creates a snapshot zip and stores metadata', function () {
    $index = makeIndex('Snapshot Index');

    File::ensureDirectoryExists($index->path());
    File::put($index->path().'/payload.txt', 'snapshot-data');

    $snapshot = $this->manager->createSnapshot($index);

    expect($snapshot)->toBeInstanceOf(Snapshot::class)
        ->and(File::exists($snapshot->path))->toBeTrue()
        ->and($snapshot->size)->toBeGreaterThan(0);

    $zip = new ZipArchive;
    $zip->open($snapshot->path);

    expect($zip->locateName('payload.txt'))->not()->toBeFalse();

    $zip->close();
});

it('deletes a snapshot file and record', function () {
    $index = makeIndex('Delete Snapshot');

    File::ensureDirectoryExists($index->path());
    File::put($index->path().'/payload.txt', 'snapshot-data');

    $snapshot = $this->manager->createSnapshot($index);

    $this->manager->deleteSnapshot($snapshot);

    expect(File::exists($snapshot->path))->toBeFalse()
        ->and(Snapshot::query()->find($snapshot->id))->toBeNull();
});

it('restores a snapshot into a new index', function () {
    $snapshotRoot = config('helix.storage.snapshot_root');
    $indexRoot = config('helix.storage.index_root');
    $testRoot = dirname($indexRoot);

    $zipPath = storage_path("{$snapshotRoot}/restore.zip");
    $sourceDir = storage_path("{$testRoot}/source");

    File::ensureDirectoryExists($sourceDir);
    File::ensureDirectoryExists(dirname($zipPath));
    File::put($sourceDir.'/payload.txt', 'snapshot-data');

    $zip = new ZipArchive;
    $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
    $zip->addFile($sourceDir.'/payload.txt', 'payload.txt');
    $zip->close();

    $this->engine->shouldReceive('create')
        ->once()
        ->with(\Mockery::type(Index::class));

    $index = $this->manager->restoreSnapshot('Restored Index', $zipPath, 256);

    expect($index->name)->toBe('Restored Index')
        ->and($index->dimension)->toBe(256)
        ->and(File::exists($index->path().'/payload.txt'))->toBeTrue();
});

it('throws when restoring to an existing index path', function () {
    $snapshotRoot = config('helix.storage.snapshot_root');
    $indexRoot = config('helix.storage.index_root');

    $zipPath = storage_path("{$snapshotRoot}/restore.zip");
    File::ensureDirectoryExists(dirname($zipPath));
    File::put($zipPath, 'not-a-zip');

    $target = storage_path("{$indexRoot}/restored-index");
    File::ensureDirectoryExists($target);

    expect(fn () => $this->manager->restoreSnapshot('Restored Index', $zipPath, 256))
        ->toThrow(RuntimeException::class);
});
