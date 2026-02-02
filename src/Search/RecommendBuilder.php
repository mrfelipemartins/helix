<?php

namespace MrFelipeMartins\Helix\Search;

use InvalidArgumentException;
use MrFelipeMartins\Helix\Models\Index;

class RecommendBuilder extends SearchBuilder
{
    /** @var array<int, string|array<int,float>> */
    protected array $positive = [];

    /** @var array<int, string|array<int,float>> */
    protected array $negative = [];

    protected string $strategy = 'average_vector';

    /**
     * @param  string|array<int, float>  ...$examples
     */
    public function positive(string|array ...$examples): self
    {
        foreach ($examples as $example) {
            $this->positive[] = $this->normalizeExample($example);
        }

        return $this;
    }

    /**
     * @param  string|array<int, float>  ...$examples
     */
    public function negative(string|array ...$examples): self
    {
        foreach ($examples as $example) {
            $this->negative[] = $this->normalizeExample($example);
        }

        return $this;
    }

    /**
     * @param  array<int, string|int>  $ids
     */
    public function positiveIds(array $ids): self
    {
        foreach ($ids as $id) {
            $this->positive[] = (string) $id;
        }

        return $this;
    }

    /**
     * @param  array<int, string|int>  $ids
     */
    public function negativeIds(array $ids): self
    {
        foreach ($ids as $id) {
            $this->negative[] = (string) $id;
        }

        return $this;
    }

    /**
     * @param  array<int, array<int,float>>  $vectors
     */
    public function positiveVectors(array $vectors): self
    {
        foreach ($vectors as $vector) {
            $this->positive[] = $this->normalizeVector($vector);
        }

        return $this;
    }

    /**
     * @param  array<int, array<int,float>>  $vectors
     */
    public function negativeVectors(array $vectors): self
    {
        foreach ($vectors as $vector) {
            $this->negative[] = $this->normalizeVector($vector);
        }

        return $this;
    }

    public function strategy(string $strategy): self
    {
        $strategy = strtolower($strategy);

        if ($strategy !== 'average_vector') {
            throw new InvalidArgumentException("Recommendation strategy [{$strategy}] is not supported yet.");
        }

        $this->strategy = $strategy;

        return $this;
    }

    /**
     * @return array<int, array{id: string, score?: float, vector?: array<int,float>, metadata?: mixed}>
     */
    public function get(): array
    {
        if ($this->index === null) {
            throw new InvalidArgumentException('RecommendBuilder requires an index.');
        }

        $index = $this->resolveIndex($this->index);
        $this->vector = $this->buildVector($index);
        $this->meta([
            'source' => 'recommend',
            'strategy' => $this->strategy,
            'positive_count' => count($this->positive),
            'negative_count' => count($this->negative),
        ]);

        return parent::get();
    }

    /**
     * @return array<int,float>
     */
    protected function buildVector(Index $index): array
    {
        if ($this->strategy !== 'average_vector') {
            throw new InvalidArgumentException("Recommendation strategy [{$this->strategy}] is not supported yet.");
        }

        $positive = $this->resolveExamples($index, $this->positive);

        if ($positive === []) {
            throw new InvalidArgumentException('Recommendation requires at least one positive example.');
        }

        $negative = $this->resolveExamples($index, $this->negative);

        $avgPositive = $this->averageVectors($positive);

        if ($negative === []) {
            return $avgPositive;
        }

        $avgNegative = $this->averageVectors($negative);

        return $this->combineAverageVector($avgPositive, $avgNegative);
    }

    /**
     * @param  array<int, string|array<int,float>>  $examples
     * @return array<int, array<int,float>>
     */
    protected function resolveExamples(Index $index, array $examples): array
    {
        $vectors = [];

        foreach ($examples as $example) {
            if (is_array($example)) {
                $vectors[] = $example;

                continue;
            }

            $vector = $this->indexes->vector($index, (string) $example);
            if ($vector === null) {
                throw new InvalidArgumentException("Unable to resolve vector for id [{$example}].");
            }

            $vectors[] = $vector;
        }

        return $vectors;
    }

    /**
     * @param  string|array<int, float>  $example
     * @return string|array<int, float>
     */
    protected function normalizeExample(string|array $example): string|array
    {
        if (is_string($example)) {
            return $example;
        }

        return $this->normalizeVector($example);
    }

    /**
     * @param  array<int, float>  $vector
     * @return array<int, float>
     */
    protected function normalizeVector(array $vector): array
    {
        return array_map('floatval', $vector);
    }

    /**
     * @param  array<int, array<int,float>>  $vectors
     * @return array<int,float>
     */
    protected function averageVectors(array $vectors): array
    {
        $count = count($vectors);
        $dimensions = count($vectors[0]);

        $sum = array_fill(0, $dimensions, 0.0);

        foreach ($vectors as $vector) {
            foreach ($vector as $i => $value) {
                $sum[$i] += $value;
            }
        }

        return array_map(fn (float $value): float => $value / $count, $sum);
    }

    /**
     * @param  array<int,float>  $positive
     * @param  array<int,float>  $negative
     * @return array<int,float>
     */
    protected function combineAverageVector(array $positive, array $negative): array
    {
        $combined = [];

        foreach ($positive as $i => $value) {
            $combined[$i] = (2 * $value) - ($negative[$i] ?? 0.0);
        }

        return $combined;
    }
}
