<?php

return [

    'enabled' => env('HELIX_ENABLED', true),

    'path' => env('HELIX_PATH', 'helix'),

    'middleware' => [
        'web',
    ],

    'gate' => env('HELIX_GATE', true),

    'activity' => [
        'table' => env('HELIX_ACTIVITY_TABLE', 'helix_activities'),
        'enabled' => env('HELIX_ACTIVITY', true),
        'sample' => (float) env('HELIX_ACTIVITY_SAMPLE', 1.0),
    ],

    'api' => [
        'enabled' => env('HELIX_API', false),
        'middleware' => [
            'api',
        ],
    ],

    'storage' => [
        'index_disk' => env('HELIX_INDEX_DISK', 'local'),
        'index_root' => env('HELIX_INDEX_ROOT', 'helix/indexes'),
        'snapshot_disk' => env('HELIX_SNAPSHOT_DISK', 'local'),
        'snapshot_root' => env('HELIX_SNAPSHOT_ROOT', 'helix/snapshots'),
    ],

];
