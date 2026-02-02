<?php

namespace MrFelipeMartins\Helix;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use InvalidArgumentException;
use MrFelipeMartins\Helix\Managers\IndexManager;
use MrFelipeMartins\Helix\Models\Index;
use MrFelipeMartins\Helix\Models\Snapshot;
use MrFelipeMartins\Helix\Search\RecommendBuilder;
use MrFelipeMartins\Helix\Search\SearchBuilder;
use RuntimeException;

class Helix
{
    /** @var array<int, string|Htmlable> */
    protected array $css = [__DIR__.'/../dist/helix.css'];

    public function __construct(
        protected IndexManager $indexes,
    ) {}

    /**
     * Manage the dashboard CSS that should be inlined on the page.
     *
     * Passing a path/Htmlable appends it; calling without arguments renders all CSS inline.
     *
     * @param  array<int, string|Htmlable>|string|Htmlable|null  $css
     */
    public function css(string|Htmlable|array|null $css = null): string|self
    {
        if (func_num_args() === 1) {
            $this->css = array_values(array_unique(array_merge($this->css, Arr::wrap($css)), SORT_REGULAR));

            return $this;
        }

        $output = '';

        foreach ($this->css as $cssPath) {
            if ($cssPath instanceof Htmlable) {
                $output .= Str::finish($cssPath->toHtml(), PHP_EOL);

                continue;
            }

            $contents = @file_get_contents($cssPath);

            if ($contents === false) {
                throw new RuntimeException("Unable to load Helix dashboard CSS path [$cssPath].");
            }

            $output .= "<style>{$contents}</style>".PHP_EOL;
        }

        return $output;
    }

    public function js(): string
    {
        if (($helix = @file_get_contents(__DIR__.'/../dist/helix.js')) === false) {
            throw new RuntimeException('Unable to load the Helix dashboard JavaScript.');
        }

        return "<script>{$helix}</script>".PHP_EOL;
    }

    public function isEnabled(): bool
    {
        return (bool) config('helix.enabled', true);
    }

    public function path(): string
    {
        $path = config('helix.path');
        $path = is_string($path) ? $path : 'helix';

        return '/'.trim($path, '/');
    }

    /**
     * Middleware stack used by the Helix dashboard routes.
     *
     * @return array<int, string>
     */
    public function middleware(): array
    {
        $middleware = config('helix.middleware');
        $middleware = is_array($middleware) ? $middleware : ['web'];

        return array_values(array_filter($middleware, 'is_string'));
    }

    /**
     * Check whether the given user (or current user) can view Helix.
     */
    public function canView(?Authenticatable $user = null): bool
    {
        $user = $user ?? Auth::user();

        return Gate::forUser($user)->allows('viewHelix');
    }

    /**
     * Create and register a new index on disk and in the database.
     */
    public function createIndex(string $name, int $dimension = 1536, ?string $path = null): Index
    {
        return $this->indexes->create($name, $dimension, $path);
    }

    /**
     * @param  array<int,float>  $vector
     *
     * Insert a record into the given index.
     */
    public function insert(Index|string $index, string $id, array $vector, mixed $metadata = null): void
    {
        $this->indexes->insert($this->resolveIndex($index), $id, $vector, $metadata);
    }

    /**
     * @param  array<int,float>  $vector
     * @return array<int, array{id: string, score?: float, vector?: array<int,float>, metadata?: mixed}>
     *
     * Execute a similarity search on the index.
     */
    public function search(Index|string|null $index = null, ?array $vector = null, int $limit = 10): array|SearchBuilder
    {
        if ($index === null && $vector === null) {
            return new SearchBuilder($this->indexes);
        }

        if ($index === null || $vector === null) {
            throw new InvalidArgumentException('Helix::search() requires both index and vector when used without the builder.');
        }

        return $this->indexes->search($this->resolveIndex($index), $vector, $limit, [
            'source' => 'direct',
        ]);
    }

    public function recommend(): RecommendBuilder
    {
        return new RecommendBuilder($this->indexes);
    }

    /**
     * @return LengthAwarePaginator<int, array{id: string, vector: array<int,float>, metadata?: mixed}>
     *
     * List and paginate raw records from the index.
     */
    public function list(Index|string $index, int $page = 1, int $perPage = 50): LengthAwarePaginator
    {
        return $this->indexes->list($this->resolveIndex($index), $page, $perPage);
    }

    /**
     * Delete a record from the target index.
     */
    public function delete(Index|string $index, string $id): void
    {
        $this->indexes->delete($this->resolveIndex($index), $id);
    }

    /**
     * Retrieve storage and record statistics for an index.
     *
     * @return array<string, mixed>
     */
    public function stats(Index|string $index): array
    {
        return $this->indexes->stats($this->resolveIndex($index));
    }

    /**
     * Run an optimize/vacuum on the index.
     */
    public function optimize(Index|string $index): void
    {
        $this->indexes->optimize($this->resolveIndex($index));
    }

    /**
     * Remove an index from disk and the database.
     */
    public function drop(Index|string $index): void
    {
        $this->indexes->drop($this->resolveIndex($index));
    }

    /**
     * Create a snapshot zip for the given index and persist metadata.
     */
    public function createSnapshot(Index|string $index): Snapshot
    {
        return $this->indexes->createSnapshot($this->resolveIndex($index));
    }

    /**
     * Delete a previously created snapshot.
     */
    public function deleteSnapshot(Snapshot $snapshot): void
    {
        $this->indexes->deleteSnapshot($snapshot);
    }

    /**
     * Restore an index from a snapshot zip, creating a new index entry.
     */
    public function restoreSnapshot(string $name, string $zipPath, int $dimension = 1536): Index
    {
        return $this->indexes->restoreSnapshot($name, $zipPath, $dimension);
    }

    protected function resolveIndex(Index|string $index): Index
    {
        if ($index instanceof Index) {
            return $index;
        }

        return Index::query()->where('name', $index)->firstOrFail();
    }
}
