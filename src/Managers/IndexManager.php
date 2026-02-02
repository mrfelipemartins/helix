<?php

namespace MrFelipeMartins\Helix\Managers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use MrFelipeMartins\Helix\Engines\Contracts\VectorEngine;
use MrFelipeMartins\Helix\Enums\IndexStatus;
use MrFelipeMartins\Helix\Events\IndexCreated;
use MrFelipeMartins\Helix\Events\IndexDropped;
use MrFelipeMartins\Helix\Events\RecordDeleted;
use MrFelipeMartins\Helix\Events\RecordInserted;
use MrFelipeMartins\Helix\Events\VectorSearchPerformed;
use MrFelipeMartins\Helix\Models\Index;
use MrFelipeMartins\Helix\Models\Snapshot;
use ZipArchive;

class IndexManager
{
    public function __construct(
        protected VectorEngine $engine,
    ) {}

    /**
     * Create a new index folder on the configured disk and persist it.
     *
     * @param  string  $name  Human-friendly name (used in slugged folder path)
     * @param  int  $dimension  Vector dimension for the index
     * @param  string|null  $path  Custom relative path on the configured disk
     */
    public function create(string $name, int $dimension = 1536, ?string $path = null): Index
    {
        $folder = Str::slug($name);
        $disk = config('helix.storage.index_disk');
        $root = config('helix.storage.index_root');
        $disk = is_string($disk) ? $disk : 'local';
        $root = is_string($root) ? $root : 'helix/indexes';

        $resolvedPath = $path ?? "{$root}/{$folder}";
        $absolute = $disk === 'local'
            ? storage_path($resolvedPath)
            : Storage::disk($disk)->path($resolvedPath);

        File::ensureDirectoryExists($absolute);

        $store = Index::query()->create([
            'name' => $name,
            'dimension' => $dimension,
            'path' => $resolvedPath,
            'status' => IndexStatus::READY->value,
        ]);

        $this->engine->create($store);

        event(new IndexCreated($store));

        return $store;
    }

    /**
     * @param  array<int,float>  $vector
     *
     * Insert (or upsert) a record into the index.
     */
    public function insert(Index $store, string $id, array $vector, mixed $metadata = null): void
    {
        try {
            $this->engine->insert($store, $id, $vector, $metadata);
            $this->markStatus($store, IndexStatus::READY);
        } catch (\Throwable $e) {
            $this->markStatus($store, IndexStatus::ERROR);
            throw $e;
        }

        event(new RecordInserted($store, $id, $vector, $metadata));
    }

    /**
     * @param  array<int,float>  $vector
     * @return array<int, array{id: string, score?: float, vector?: array<int,float>, metadata?: mixed}>
     *
     * Run a similarity search against the index.
     */
    /**
     * @param  array<string, mixed>  $meta
     */
    public function search(Index $store, array $vector, int $limit = 10, array $meta = []): array
    {
        $start = microtime(true);

        try {
            $results = $this->engine->search($store, $vector, $limit);
            $this->markStatus($store, IndexStatus::READY);
        } catch (\Throwable $e) {
            $this->markStatus($store, IndexStatus::ERROR);
            throw $e;
        }

        $durationMs = (microtime(true) - $start) * 1000;

        event(new VectorSearchPerformed($store, $vector, $limit, $results, $durationMs, $meta));

        return $results;
    }

    /**
     * @return array<int,float>|null
     */
    public function vector(Index $store, string $id): ?array
    {
        return $this->engine->vector($store, $id);
    }

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, array{id: string, vector: array<int,float>, metadata?: mixed}>
     *
     * List raw records from the index without performing ANN search.
     */
    public function list(Index $store, int $page = 1, int $perPage = 50): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        try {
            $paginator = $this->engine->list($store, $page, $perPage);
            $this->markStatus($store, IndexStatus::READY);

            return $paginator;
        } catch (\Throwable $e) {
            $this->markStatus($store, IndexStatus::ERROR);
            throw $e;
        }
    }

    public function delete(Index $store, string $id): void
    {
        try {
            $this->engine->delete($store, $id);
            $this->markStatus($store, IndexStatus::READY);
        } catch (\Throwable $e) {
            $this->markStatus($store, IndexStatus::ERROR);
            throw $e;
        }

        event(new RecordDeleted($store, $id));
    }

    /**
     * @return array<string, mixed>
     *
     * Gather size and record counts for the index.
     */
    public function stats(Index $store): array
    {
        try {
            $stats = $this->engine->stats($store);
            $this->markStatus($store, IndexStatus::READY);

            return $stats;
        } catch (\Throwable $e) {
            $this->markStatus($store, IndexStatus::ERROR);
            throw $e;
        }
    }

    public function optimize(Index $store): void
    {
        $this->markStatus($store, IndexStatus::OPTIMIZING);

        try {
            $this->engine->optimize($store);
            $this->markStatus($store, IndexStatus::READY);
        } catch (\Throwable $e) {
            $this->markStatus($store, IndexStatus::ERROR);
            throw $e;
        }
    }

    /**
     * Drop an index from disk and remove its database record.
     */
    public function drop(Index $store): void
    {
        try {
            $this->engine->drop($store);
        } catch (\Throwable $e) {
            $this->markStatus($store, IndexStatus::ERROR);
            throw $e;
        }

        event(new IndexDropped($store));

        $store->delete();
    }

    /**
     * Create a zip snapshot for the given index on the configured snapshot disk.
     */
    public function createSnapshot(Index $index): Snapshot
    {
        $name = now()->format('Ymd_His');
        $snapshotDisk = config('helix.storage.snapshot_disk');
        $snapshotRoot = config('helix.storage.snapshot_root');
        $snapshotDisk = is_string($snapshotDisk) ? $snapshotDisk : 'local';
        $snapshotRoot = is_string($snapshotRoot) ? $snapshotRoot : 'helix/snapshots';
        $basePath = "{$snapshotRoot}/{$index->id}";
        $absoluteBase = $snapshotDisk === 'local'
            ? storage_path($basePath)
            : Storage::disk($snapshotDisk)->path($basePath);

        File::ensureDirectoryExists($absoluteBase);

        $zipPath = "{$absoluteBase}/{$name}.zip";

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException("Unable to create snapshot zip at {$zipPath}");
        }

        $source = realpath($index->path());
        if ($source === false) {
            throw new \RuntimeException('Unable to locate index path for snapshot.');
        }
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($files as $file) {
            if (! $file instanceof \SplFileInfo) {
                continue;
            }

            $filePath = $file->getRealPath();
            if ($filePath === false) {
                continue;
            }

            $relativePath = substr($filePath, strlen($source) + 1);

            if ($file->isDir()) {
                $zip->addEmptyDir($relativePath);
            } else {
                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();

        $size = file_exists($zipPath) ? filesize($zipPath) : 0;

        return Snapshot::query()->create([
            'index_id' => $index->id,
            'name' => $name,
            'path' => $snapshotDisk === 'local'
                ? $zipPath
                : "{$basePath}/{$name}.zip",
            'size' => $size,
        ]);
    }

    public function deleteSnapshot(Snapshot $snapshot): void
    {
        $snapshotDisk = config('helix.storage.snapshot_disk');
        $snapshotDisk = is_string($snapshotDisk) ? $snapshotDisk : 'local';
        $path = $snapshotDisk === 'local'
            ? $snapshot->path
            : Storage::disk($snapshotDisk)->path($snapshot->path);

        if (File::isDirectory($path)) {
            File::deleteDirectory($path);
        } else {
            File::delete($path);
        }
        $snapshot->delete();
    }

    /**
     * Restore a new index from a snapshot zip, returning the created Index.
     */
    public function restoreSnapshot(string $name, string $zipPath, int $dimension = 1536): Index
    {
        $slug = Str::slug($name);
        $disk = config('helix.storage.index_disk');
        $root = config('helix.storage.index_root');
        $disk = is_string($disk) ? $disk : 'local';
        $root = is_string($root) ? $root : 'helix/indexes';
        $targetRelative = "{$root}/{$slug}";
        $targetPath = $disk === 'local'
            ? storage_path($targetRelative)
            : Storage::disk($disk)->path($targetRelative);

        if (File::exists($targetPath)) {
            throw new \RuntimeException("Index path already exists at {$targetPath}");
        }

        File::ensureDirectoryExists($targetPath);

        $zip = new ZipArchive;
        if ($zip->open($zipPath) !== true) {
            throw new \RuntimeException("Unable to open snapshot zip at {$zipPath}");
        }

        $zip->extractTo($targetPath);
        $zip->close();

        return $this->create($name, $dimension, $targetRelative);
    }

    protected function markStatus(Index $index, IndexStatus $status): void
    {
        $index->status = $status;
        $index->save();
    }
}
