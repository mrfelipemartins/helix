<?php

namespace MrFelipeMartins\Helix\Models;

use Illuminate\Database\Eloquent\Model;
use MrFelipeMartins\Helix\Enums\ActivityLevel;
use MrFelipeMartins\Helix\Enums\ActivityType;

/**
 * @property int $id
 * @property string $index
 * @property ActivityType $type
 * @property ActivityLevel $level
 * @property string|null $message
 * @property array|null $meta
 * @property \Illuminate\Support\Carbon $created_at
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class VectorActivity extends Model
{
    protected $table = 'helix_activities';

    public $timestamps = false;

    /** @var array<int, string> */
    protected $fillable = [
        'index',
        'type',
        'level',
        'message',
        'meta',
        'created_at',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'meta' => 'array',
        'created_at' => 'datetime',
        'type' => ActivityType::class,
        'level' => ActivityLevel::class,
    ];
}
