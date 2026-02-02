<?php

namespace MrFelipeMartins\Helix\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string css(string|\Illuminate\Contracts\Support\Htmlable|array|null $css = null)
 * @method static string js()
 * @method static bool isEnabled()
 * @method static string path()
 * @method static array<int,string> middleware()
 * @method static bool canView(\Illuminate\Contracts\Auth\Authenticatable|null $user = null)
 * @method static \MrFelipeMartins\Helix\Models\Index createIndex(string $name, int $dimension = 1536, ?string $path = null)
 * @method static void insert(\MrFelipeMartins\Helix\Models\Index|string $index, string $id, array $vector, mixed $metadata = null)
 * @method static \MrFelipeMartins\Helix\Search\SearchBuilder search()
 * @method static array<int, array{id: string, score?: float, vector?: array<int,float>, metadata?: mixed}> search(\MrFelipeMartins\Helix\Models\Index|string $index, array $vector, int $limit = 10)
 * @method static \MrFelipeMartins\Helix\Search\RecommendBuilder recommend()
 * @method static \Illuminate\Contracts\Pagination\LengthAwarePaginator list(\MrFelipeMartins\Helix\Models\Index|string $index, int $page = 1, int $perPage = 50)
 * @method static void delete(\MrFelipeMartins\Helix\Models\Index|string $index, string $id)
 * @method static array<string, mixed> stats(\MrFelipeMartins\Helix\Models\Index|string $index)
 * @method static void optimize(\MrFelipeMartins\Helix\Models\Index|string $index)
 * @method static void drop(\MrFelipeMartins\Helix\Models\Index|string $index)
 * @method static \MrFelipeMartins\Helix\Models\Snapshot createSnapshot(\MrFelipeMartins\Helix\Models\Index|string $index)
 * @method static void deleteSnapshot(\MrFelipeMartins\Helix\Models\Snapshot $snapshot)
 * @method static \MrFelipeMartins\Helix\Models\Index restoreSnapshot(string $name, string $zipPath, int $dimension = 1536)
 */
class Helix extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \MrFelipeMartins\Helix\Helix::class;
    }
}
