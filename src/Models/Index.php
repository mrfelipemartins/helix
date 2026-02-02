<?php

namespace MrFelipeMartins\Helix\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use MrFelipeMartins\Helix\Enums\IndexStatus;
use MrFelipeMartins\Helix\Facades\Helix;
use MrFelipeMartins\Helix\Support\Helpers;

/**
 * @property int $id
 * @property string $name
 * @property int $dimension
 * @property string|null $path
 * @property IndexStatus $status
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Index extends Model
{
    protected $table = 'helix_indexes';

    /** @var array<int, string> */
    protected $fillable = [
        'name',
        'dimension',
        'path',
        'status',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'dimension' => 'integer',
        'status' => IndexStatus::class,
    ];

    /** @var array<string, mixed> */
    protected $attributes = [
        'dimension' => 1536,
        'status' => IndexStatus::READY->value,
    ];

    public function path(): string
    {
        $disk = config('helix.storage.index_disk');
        $root = config('helix.storage.index_root');
        $disk = is_string($disk) ? $disk : 'local';
        $root = is_string($root) ? $root : 'helix/indexes';

        $base = $this->path ?? "{$root}/{$this->name}";

        return $disk === 'local'
            ? storage_path($base)
            : Storage::disk($disk)->path($base);
    }

    public function getSizeAttribute(): string
    {
        $stats = Helix::stats($this);

        $storage = is_array($stats['storage'] ?? null) ? $stats['storage'] : [];

        $size = ($storage['vector_file_bytes'] ?? 0)
            + ($storage['graph_file_bytes'] ?? 0)
            + ($storage['meta_file_bytes'] ?? 0);

        return Helpers::formatBytes($size);
    }
}
