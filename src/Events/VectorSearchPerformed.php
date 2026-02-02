<?php

namespace MrFelipeMartins\Helix\Events;

use MrFelipeMartins\Helix\Models\Index;

class VectorSearchPerformed
{
    public function __construct(
        public Index $store,
        /** @var array<int,float> */
        public array $queryVector,
        public int $limit,
        /** @var array<int, array{id: string, score?: float, vector?: array<int,float>, metadata?: mixed}> */
        public array $results,
        public float $durationMs,
        /** @var array<string, mixed> */
        public array $meta = [],
    ) {}
}
