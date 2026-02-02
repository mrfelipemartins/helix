<p align="center"><img src="/art/logo.svg" alt="Helix Logo"></p>

Helix is a vector similarity search engine and dashboard for Laravel.

## Features

- Create, inspect, and manage vector indexes
- Fast similarity search with a fluent builder API
- Recommendation queries with positive/negative examples
- Snapshots (create, restore, delete)
- Activity logging with latency tracking

## Requirements

- PHP 8.2+
- Laravel 11 or 12

## Installation

```bash
composer require mrfelipemartins/helix
php artisan helix:install
php artisan vendor:publish --tag=helix-migrations
php artisan migrate
```

Optional publishes (config only):

```bash
php artisan vendor:publish --tag=helix-config
```

To publish Helix config and migrations in one go:

```bash
php artisan vendor:publish --tag=helix
```

## Quick start

Create an index, insert vectors, and search:

```php
use Illuminate\Support\Str;
use MrFelipeMartins\Helix\Facades\Helix;

$index = Helix::createIndex('my-index-name', 1536);

Helix::insert('my-index-name', (string) Str::uuid(), $embedding, [
    'document_id' => 1,
    'text' => 'Vector databases are optimized for similarity search.',
]);

$results = Helix::search(index: 'my-index-name', vector: $embedding,  limit: 5);
```

## Search builder

The builder adds filters, offsets, score thresholds, and custom metadata.

```php
$results = Helix::search()
    ->on('my-index-name')
    ->query($vector)
    ->where('document_id', 1)
    ->scoreThreshold(0.85)
    ->limit(5)
    ->get();
```

Filtering examples:

```php
$results = Helix::search()
    ->on('my-index-name')
    ->query($vector)
    ->where('author.id', 123)
    ->whereBetween('score', [0.2, 0.8])
    ->whereNotNull('summary')
    ->limit(10)
    ->get();
```

## Recommendations

Use positive and negative examples. IDs are resolved from the index; raw vectors are accepted too.

```php
$results = Helix::recommend()
    ->on('my-index-name')
    ->positiveIds(['doc-1', 'doc-2'])
    ->negativeVectors([
        [0.2, 0.3, 0.4, 0.5],
    ])
    ->limit(5)
    ->get();
```

## Snapshots

```php
$snapshot = Helix::createSnapshot('my-index-name');
Helix::restoreSnapshot('restored-index', $snapshot->path, 1536);
Helix::deleteSnapshot($snapshot);
```

Snapshots are stored as zip archives on the configured snapshot disk.

## Activity logging and latency

Helix records operational activity (create, insert, search, delete, etc). Search events capture latency in milliseconds and include request context metadata.

Tuning:

- `HELIX_ACTIVITY=true|false`
- `HELIX_ACTIVITY_SAMPLE=0.1` (10% sampling)

Prune old activity rows:

```bash
php artisan helix:prune-activities --days=30
php artisan helix:prune-activities --before="2024-01-01"
php artisan helix:prune-activities --dry-run
```

## Dashboard

Visit `/<HELIX_PATH>` (default: `/helix`).

Access control:

```php
use Illuminate\Support\Facades\Gate;

Gate::define('viewHelix', fn ($user) => $user?->isAdmin());
```

Notes:

- By default, Helix defines `viewHelix` to allow access only in the `local` environment. Override it in your app.
- You can disable the gate entirely with `HELIX_GATE=false` and/or customize the middleware stack.

## Configuration

Publish the config (`php artisan vendor:publish --tag=helix-config`) to customize:

```php
return [
    'enabled' => env('HELIX_ENABLED', true),
    'path' => env('HELIX_PATH', 'helix'),
    'middleware' => ['web'],
    'gate' => env('HELIX_GATE', true),
    'activity' => [
        'table' => env('HELIX_ACTIVITY_TABLE', 'helix_activities'),
        'enabled' => env('HELIX_ACTIVITY', true),
        'sample' => (float) env('HELIX_ACTIVITY_SAMPLE', 1.0),
    ],
    'storage' => [
        'index_disk' => env('HELIX_INDEX_DISK', 'local'),
        'index_root' => env('HELIX_INDEX_ROOT', 'helix/indexes'),
        'snapshot_disk' => env('HELIX_SNAPSHOT_DISK', 'local'),
        'snapshot_root' => env('HELIX_SNAPSHOT_ROOT', 'helix/snapshots'),
    ],
];
```

## Storage layout

- Index files live at `<disk>:<index_root>/<slug>` (default `storage/helix/indexes/...` on the local disk).
- Snapshot zips are stored under `<snapshot_root>/<index_id>/YYYYmmdd_HHMMSS.zip`.

## Commands

- `helix:install` - publish config and assets
- `helix:optimize` - optimize all indexes
- `helix:prune-activities` - delete old activity rows

## Use cases

- Semantic search over documents
- Recommendations and similarity matching
- RAG / embedding-based retrieval

## Testing

```bash
composer test
composer lint
```

## Contributing

Issues and PRs are welcome. Keep changes focused, add tests when it makes sense, and follow the existing code style.

## License

MIT
