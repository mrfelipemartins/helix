<?php

namespace MrFelipeMartins\Helix\Engines;

use Centamiv\Vektor\Core\Config;
use Centamiv\Vektor\Services\Indexer;
use Centamiv\Vektor\Services\Optimizer;
use Centamiv\Vektor\Services\Searcher;
use Centamiv\Vektor\Storage\Binary\MetaFile;
use Centamiv\Vektor\Storage\Binary\PayloadFile;
use Centamiv\Vektor\Storage\Binary\VectorFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\File;
use MrFelipeMartins\Helix\Engines\Contracts\VectorEngine;
use MrFelipeMartins\Helix\Models\Index;

class VektorEngine implements VectorEngine
{
    /**
     * Initialize an index on disk by creating the lock file and configuring paths.
     */
    public function create(Index $store): void
    {
        $this->configure($store);

        File::put(Config::getLockFile(), '', true);
    }

    /**
     * @param  array<int,float>  $vector
     *
     * Persist a record and optional metadata into the index.
     */
    public function insert(Index $store, string $id, array $vector, mixed $metadata = null): void
    {
        $this->configure($store);

        (new Indexer)->insert($id, $vector, $metadata);
    }

    /**
     * @param  array<int,float>  $vector
     * @return array<int, array{id: string, score?: float, vector?: array<int,float>, metadata?: mixed}>
     *
     * Perform an ANN search and hydrate metadata when available.
     */
    public function search(Index $store, array $vector, int $limit = 10): array
    {
        $this->configure($store);

        $results = (new Searcher)->search($vector, $limit);
        $metaFile = new MetaFile;
        $payloadFile = new PayloadFile;

        return array_map(function ($result) use ($metaFile, $payloadFile) {
            $metadata = $this->readMetadata($metaFile, $payloadFile, $result['id']);

            return $metadata === null
                ? $result
                : $result + ['metadata' => $metadata];
        }, $results);
    }

    /**
     * @return array<int,float>|null
     */
    public function vector(Index $store, string $id): ?array
    {
        $this->configure($store);

        $metaFile = new MetaFile;
        $vectorFile = new VectorFile;

        $internalId = $metaFile->find($id);
        if ($internalId === null) {
            return null;
        }

        return $vectorFile->readVectorOnly($internalId);
    }

    /**
     * @return LengthAwarePaginator<array-key, array{id: string, vector: array<int,float>, metadata?: mixed}>
     *
     * Iterate the underlying vector file to return raw records with pagination.
     */
    public function list(Index $store, int $page = 1, int $perPage = 50): LengthAwarePaginator
    {
        $this->configure($store);

        $page = max($page, 1);
        $perPage = max($perPage, 1);

        $vectorFile = new VectorFile;
        $metaFile = new MetaFile;
        $payloadFile = new PayloadFile;
        $data = [];
        $total = 0;
        $start = ($page - 1) * $perPage;
        $end = $start + $perPage;

        foreach ($vectorFile->scan() as $index => $record) {
            $total++;

            if ($index < $start || $index >= $end) {
                continue;
            }

            $record = [
                'id' => $record['id'],
                'vector' => $record['vector'],
            ];

            $metadata = $this->readMetadata($metaFile, $payloadFile, $record['id']);
            if ($metadata !== null) {
                $record['metadata'] = $metadata;
            }

            $data[] = $record;
        }

        return new LengthAwarePaginator(
            $data,
            $total,
            $perPage,
            $page,
            ['path' => Paginator::resolveCurrentPath()]
        );
    }

    public function delete(Index $store, string $id): void
    {
        $this->configure($store);

        (new Indexer)->delete($id);
    }

    public function stats(Index $store): array
    {
        $this->configure($store);

        return (new Indexer)->getStats();
    }

    public function optimize(Index $store): void
    {
        $this->configure($store);

        (new Optimizer)->run();
    }

    public function drop(Index $store): void
    {
        $this->configure($store);

        File::deleteDirectory($store->path());
    }

    protected function configure(Index $store): void
    {
        $path = $store->path();

        File::ensureDirectoryExists($path);

        Config::setDataDir($path);
        Config::setDimensions((int) $store->dimension);
    }

    protected function readMetadata(MetaFile $metaFile, PayloadFile $payloadFile, string $externalId): mixed
    {
        $entry = $metaFile->findEntry($externalId);
        if ($entry === null || $entry['payload_length'] <= 0) {
            return null;
        }

        return $payloadFile->read($entry['payload_offset'], $entry['payload_length']);
    }
}
