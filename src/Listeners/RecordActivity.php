<?php

namespace MrFelipeMartins\Helix\Listeners;

use MrFelipeMartins\Helix\Enums\ActivityLevel;
use MrFelipeMartins\Helix\Enums\ActivityType;
use MrFelipeMartins\Helix\Events\IndexCreated;
use MrFelipeMartins\Helix\Events\IndexDropped;
use MrFelipeMartins\Helix\Events\RecordDeleted;
use MrFelipeMartins\Helix\Events\RecordInserted;
use MrFelipeMartins\Helix\Events\VectorSearchPerformed;
use MrFelipeMartins\Helix\Models\VectorActivity;

class RecordActivity
{
    public function handle(object $event): void
    {
        $config = (array) config('helix.activity', []);

        if (empty($config['enabled'])) {
            return;
        }

        $sample = (float) ($config['sample'] ?? 1.0);
        if ($sample < 1 && mt_rand() / mt_getrandmax() > $sample) {
            return;
        }

        $entry = $this->format($event);

        if ($entry === null) {
            return;
        }

        if (function_exists('defer')) {
            defer(function () use ($entry): void {
                VectorActivity::create($entry);
            });

            return;
        }

        VectorActivity::create($entry);
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function format(object $event): ?array
    {
        return match (true) {
            $event instanceof IndexCreated => [
                'index' => $event->store->name,
                'type' => ActivityType::CREATE->value,
                'level' => ActivityLevel::INFO->value,
                'message' => 'Index created',
                'meta' => [
                    'dimension' => (int) $event->store->dimension,
                    'path' => $event->store->path(),
                ],
                'created_at' => now(),
            ],

            $event instanceof IndexDropped => [
                'index' => $event->store->name,
                'type' => ActivityType::DROP->value,
                'level' => ActivityLevel::INFO->value,
                'message' => 'Index dropped',
                'meta' => [
                    'path' => $event->store->path(),
                ],
                'created_at' => now(),
            ],

            $event instanceof RecordInserted => [
                'index' => $event->store->name,
                'type' => ActivityType::INSERT->value,
                'level' => ActivityLevel::INFO->value,
                'message' => 'Vector inserted',
                'meta' => [
                    'id' => $event->id,
                    'metadata_keys' => is_array($event->metadata) ? array_keys($event->metadata) : [],
                ],
                'created_at' => now(),
            ],

            $event instanceof RecordDeleted => [
                'index' => $event->store->name,
                'type' => ActivityType::DELETE->value,
                'level' => ActivityLevel::INFO->value,
                'message' => 'Vector deleted',
                'meta' => [
                    'id' => $event->id,
                ],
                'created_at' => now(),
            ],

            $event instanceof VectorSearchPerformed => [
                'index' => $event->store->name,
                'type' => ActivityType::SEARCH->value,
                'level' => ActivityLevel::INFO->value,
                'message' => 'Vector search executed',
                'meta' => [
                    'limit' => $event->limit,
                    'results_count' => count($event->results),
                    'latency_ms' => $event->durationMs,
                    'context' => $event->meta,
                ],
                'created_at' => now(),
            ],

            default => null,
        };
    }
}
