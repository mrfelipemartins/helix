<?php

namespace MrFelipeMartins\Helix\Search;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use InvalidArgumentException;
use MrFelipeMartins\Helix\Managers\IndexManager;
use MrFelipeMartins\Helix\Models\Index;

class SearchBuilder
{
    protected Index|string|null $index = null;

    /** @var array<int, float>|null */
    protected ?array $vector = null;

    /** @var array<int, array{type: string, key: string, operator?: string, value?: mixed}> */
    protected array $filters = [];

    protected int $limit = 10;

    protected int $offset = 0;

    protected int $oversample = 50;

    protected ?float $scoreThreshold = null;

    /** @var array<string, mixed> */
    protected array $searchMeta = [];

    public function __construct(
        protected IndexManager $indexes
    ) {}

    public function on(Index|string $index): self
    {
        $this->index = $index;

        return $this;
    }

    /**
     * @param  array<int, float>  $vector
     */
    public function query(array $vector): self
    {
        $this->vector = $vector;

        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = max(0, $limit);

        return $this;
    }

    public function offset(int $offset): self
    {
        $this->offset = max(0, $offset);

        return $this;
    }

    public function oversample(int $oversample): self
    {
        $this->oversample = max(0, $oversample);

        return $this;
    }

    public function scoreThreshold(float $threshold): self
    {
        $this->scoreThreshold = $threshold;

        return $this;
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    public function meta(array $meta): self
    {
        $this->searchMeta = array_merge($this->searchMeta, $meta);

        return $this;
    }

    public function where(string $key, mixed $operator = null, mixed $value = null): self
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        if (! is_string($operator)) {
            $operator = is_scalar($operator) ? (string) $operator : '=';
        }

        $this->filters[] = [
            'type' => 'basic',
            'key' => $key,
            'operator' => $operator,
            'value' => $value,
        ];

        return $this;
    }

    /**
     * @param  array<int, mixed>  $values
     */
    public function whereIn(string $key, array $values): self
    {
        $this->filters[] = [
            'type' => 'in',
            'key' => $key,
            'value' => $values,
        ];

        return $this;
    }

    /**
     * @param  array<int, mixed>  $values
     */
    public function whereNotIn(string $key, array $values): self
    {
        $this->filters[] = [
            'type' => 'notIn',
            'key' => $key,
            'value' => $values,
        ];

        return $this;
    }

    public function whereNull(string $key): self
    {
        $this->filters[] = [
            'type' => 'null',
            'key' => $key,
        ];

        return $this;
    }

    public function whereNotNull(string $key): self
    {
        $this->filters[] = [
            'type' => 'notNull',
            'key' => $key,
        ];

        return $this;
    }

    /**
     * @param  array<int, mixed>  $range
     */
    public function whereBetween(string $key, array $range): self
    {
        $this->filters[] = [
            'type' => 'between',
            'key' => $key,
            'value' => $range,
        ];

        return $this;
    }

    /**
     * @param  array<int, mixed>  $range
     */
    public function whereNotBetween(string $key, array $range): self
    {
        $this->filters[] = [
            'type' => 'notBetween',
            'key' => $key,
            'value' => $range,
        ];

        return $this;
    }

    /**
     * @return array<int, array{id: string, score?: float, vector?: array<int,float>, metadata?: mixed}>
     */
    public function get(): array
    {
        if ($this->index === null || $this->vector === null) {
            throw new InvalidArgumentException('SearchBuilder requires both index and query vector.');
        }

        $index = $this->resolveIndex($this->index);
        $fetchLimit = $this->calculateFetchLimit();

        $results = $this->indexes->search($index, $this->vector, $fetchLimit, $this->buildMeta($fetchLimit));

        $filtered = array_values(array_filter($results, function (array $item): bool {
            if ($this->scoreThreshold !== null && isset($item['score']) && $item['score'] < $this->scoreThreshold) {
                return false;
            }

            return $this->passesFilters($item);
        }));

        if ($this->offset > 0) {
            $filtered = array_slice($filtered, $this->offset);
        }

        if ($this->limit > 0) {
            $filtered = array_slice($filtered, 0, $this->limit);
        }

        return $filtered;
    }

    /**
     * @return array<string, mixed>
     */
    protected function buildMeta(int $fetchLimit): array
    {
        return array_merge([
            'source' => 'builder',
            'limit' => $this->limit,
            'offset' => $this->offset,
            'fetch_limit' => $fetchLimit,
            'score_threshold' => $this->scoreThreshold,
            'filters' => $this->filters,
        ], $this->searchMeta);
    }

    protected function resolveIndex(Index|string $index): Index
    {
        if ($index instanceof Index) {
            return $index;
        }

        return Index::query()->where('name', $index)->firstOrFail();
    }

    protected function calculateFetchLimit(): int
    {
        $needsOverfetch = $this->offset > 0 || $this->filters !== [] || $this->scoreThreshold !== null;
        $limit = max(1, $this->limit);

        if (! $needsOverfetch) {
            return $limit;
        }

        return $limit + $this->offset + $this->oversample;
    }

    /**
     * @param  array<string, mixed>  $item
     */
    protected function passesFilters(array $item): bool
    {
        $metadata = $item['metadata'] ?? null;

        foreach ($this->filters as $filter) {
            $actual = $metadata !== null
                ? data_get($metadata, $filter['key'])
                : null;

            if (! $this->matchesFilter($filter, $actual)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  array{type: string, key: string, operator?: string, value?: mixed}  $filter
     */
    protected function matchesFilter(array $filter, mixed $actual): bool
    {
        return match ($filter['type']) {
            'basic' => $this->matchesBasic($filter['operator'] ?? '=', $actual, $filter['value'] ?? null),
            'in' => in_array($actual, Arr::wrap($filter['value'] ?? []), true),
            'notIn' => ! in_array($actual, Arr::wrap($filter['value'] ?? []), true),
            'null' => $actual === null,
            'notNull' => $actual !== null,
            'between' => $this->matchesBetween($actual, is_array($filter['value'] ?? null) ? $filter['value'] : []),
            'notBetween' => ! $this->matchesBetween($actual, is_array($filter['value'] ?? null) ? $filter['value'] : []),
            default => false,
        };
    }

    protected function matchesBasic(string $operator, mixed $actual, mixed $expected): bool
    {
        $operator = strtolower($operator);

        return match ($operator) {
            '=', '==' => $actual == $expected,
            '!=', '<>' => $actual != $expected,
            '>' => $actual > $expected,
            '>=' => $actual >= $expected,
            '<' => $actual < $expected,
            '<=' => $actual <= $expected,
            'like' => $this->matchesLike($actual, $expected),
            default => false,
        };
    }

    /**
     * @param  array<int, mixed>  $range
     */
    protected function matchesBetween(mixed $actual, array $range): bool
    {
        if (count($range) < 2) {
            return false;
        }

        [$min, $max] = array_values($range);

        return $actual >= $min && $actual <= $max;
    }

    protected function matchesLike(mixed $actual, mixed $expected): bool
    {
        if (! is_scalar($actual) || ! is_scalar($expected)) {
            return false;
        }

        $pattern = str_replace('%', '*', (string) $expected);

        return Str::is($pattern, (string) $actual);
    }
}
