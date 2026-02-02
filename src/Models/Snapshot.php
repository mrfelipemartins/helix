<?php

namespace MrFelipeMartins\Helix\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $index_id
 * @property string $name
 * @property string $path
 * @property int $size
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Snapshot extends Model
{
    protected $table = 'helix_snapshots';

    /** @var array<int, string> */
    protected $fillable = [
        'index_id',
        'name',
        'path',
        'size',
    ];

    /**
     * @return BelongsTo<Index, Snapshot>
     */
    public function index(): BelongsTo
    {
        /** @var BelongsTo<Index, Snapshot> $relation */
        $relation = $this->belongsTo(Index::class);

        return $relation;
    }
}
