---
title: Configuration
---

# Configuration

Publish the config:

```bash
php artisan vendor:publish --tag=helix-config
```

Below is a quick explanation of each setting. These map directly to `config/helix.php`.

```php
return [
    // Enable or disable the dashboard and routes entirely.
    'enabled' => env('HELIX_ENABLED', true),

    // Base path for the dashboard routes.
    'path' => env('HELIX_PATH', 'helix'),

    // Middleware stack applied to the dashboard routes.
    'middleware' => ['web'],

    // Whether the viewHelix gate is enforced.
    'gate' => env('HELIX_GATE', true),

    'activity' => [
        // Activity table name.
        'table' => env('HELIX_ACTIVITY_TABLE', 'helix_activities'),

        // Enable or disable activity logging.
        'enabled' => env('HELIX_ACTIVITY', true),

        // Sampling rate (1.0 = 100%).
        'sample' => (float) env('HELIX_ACTIVITY_SAMPLE', 1.0),
    ],
    'storage' => [
        // Filesystem disk for indexes.
        'index_disk' => env('HELIX_INDEX_DISK', 'local'),

        // Root directory for indexes on the disk.
        'index_root' => env('HELIX_INDEX_ROOT', 'helix/indexes'),

        // Filesystem disk for snapshots.
        'snapshot_disk' => env('HELIX_SNAPSHOT_DISK', 'local'),

        // Root directory for snapshots on the disk.
        'snapshot_root' => env('HELIX_SNAPSHOT_ROOT', 'helix/snapshots'),
    ],
];
```

## Storage layout

- Index files live at `<disk>:<index_root>/<slug>`.
- Snapshots are stored under `<snapshot_root>/<index_id>/YYYYmmdd_HHMMSS.zip`.

## Common environment variables

```bash
HELIX_ENABLED=true
HELIX_PATH=helix
HELIX_GATE=true

HELIX_ACTIVITY=true
HELIX_ACTIVITY_SAMPLE=1.0

HELIX_INDEX_DISK=local
HELIX_INDEX_ROOT=helix/indexes
HELIX_SNAPSHOT_DISK=local
HELIX_SNAPSHOT_ROOT=helix/snapshots
```

## Notes

- If you disable the gate, consider adding middleware like `auth` to protect the dashboard.
- When using a non-local disk, make sure the disk supports direct file paths.
