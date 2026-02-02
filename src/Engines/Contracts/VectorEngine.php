<?php

namespace MrFelipeMartins\Helix\Engines\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use MrFelipeMartins\Helix\Models\Index;

interface VectorEngine
{
    public function create(Index $store): void;

    /**
     * @param  array<int,float>  $vector
     */
    public function insert(Index $store, string $id, array $vector, mixed $metadata = null): void;

    /**
     * @param  array<int,float>  $vector
     * @return array<int, array{id: string, score?: float, vector?: array<int,float>, metadata?: mixed}>
     */
    public function search(Index $store, array $vector, int $limit = 10): array;

    /**
     * @return array<int,float>|null
     */
    public function vector(Index $store, string $id): ?array;

    /**
     * @return LengthAwarePaginator<array-key, array{id: string, vector: array<int,float>, metadata?: mixed}>
     */
    public function list(Index $store, int $page = 1, int $perPage = 50): LengthAwarePaginator;

    public function delete(Index $store, string $id): void;

    /**
     * @return array<string, mixed>
     */
    public function stats(Index $store): array;

    public function optimize(Index $store): void;

    public function drop(Index $store): void;
}
