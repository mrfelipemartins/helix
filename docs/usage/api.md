---
title: API Reference
---

# API Reference

All APIs are available through the `MrFelipeMartins\\Helix\\Facades\\Helix` facade.

## Create an index

```php
$index = Helix::createIndex('my-index-name', 1536);
```

**Parameters**

- `name` (string): Human-friendly index name.
- `dimension` (int): Embedding dimension.
- `path` (string|null): Optional custom relative path.

## Insert a record

```php
Helix::insert('my-index-name', 'doc-1', $vector, [
    'document_id' => 1,
]);
```

**Parameters**

- `index` (Index|string): Index model or name.
- `id` (string): External record identifier.
- `vector` (array of float): Vector embedding values.
- `metadata` (mixed|null): Optional metadata stored with the record.

## Search

```php
$results = Helix::search(index: 'my-index-name', vector: $vector, limit: 10);
```

**Parameters**

- `index` (Index|string): Index model or name.
- `vector` (array of float): Query vector.
- `limit` (int): Maximum number of results.

**Returns**

An array of results: `['id' => string, 'score' => float, 'metadata' => mixed|null]`.

## Search builder

```php
$results = Helix::search()
    ->on('my-index-name')
    ->query($vector)
    ->where('document_id', 1)
    ->limit(10)
    ->get();
```

**Builder methods**

- `on(Index|string $index)`: Select the index.
- `query(array $vector)`: Set the query vector.
- `limit(int $limit)`: Result limit.
- `offset(int $offset)`: Skip N results (post-filter).
- `oversample(int $oversample)`: Fetch extra results to satisfy filters/offsets.
- `scoreThreshold(float $threshold)`: Minimum score.
- `meta(array $meta)`: Extra metadata for activity logging.
- `where(string $key, mixed $operatorOrValue, mixed $value = null)`: Filter metadata.
- `whereIn(string $key, array $values)`, `whereNotIn(...)`
- `whereNull(string $key)`, `whereNotNull(...)`
- `whereBetween(string $key, array $range)`, `whereNotBetween(...)`

## Recommendations

```php
$results = Helix::recommend()
    ->on('my-index-name')
    ->positiveIds(['doc-1'])
    ->limit(5)
    ->get();
```

**Parameters**

- `positiveIds(array<string|int>)`: IDs used for positive examples.
- `negativeIds(array<string|int>)`: IDs used for negative examples.
- `positiveVectors(array of array of float)`
- `negativeVectors(array of array of float)`
- `strategy(string)`: Currently only `average_vector`.

## List records

```php
$paginator = Helix::list('my-index-name', page: 1, perPage: 50);
```

**Parameters**

- `index` (Index|string)
- `page` (int)
- `perPage` (int)

## Delete a record

```php
Helix::delete('my-index-name', 'doc-1');
```

**Parameters**

- `index` (Index|string)
- `id` (string)

## Stats

```php
$stats = Helix::stats('my-index-name');
```

**Returns**

An array of index and storage statistics from the engine.

## Optimize

```php
Helix::optimize('my-index-name');
```

Runs an index optimization/vacuum.

## Drop an index

```php
Helix::drop('my-index-name');
```

Deletes index files and removes the database record.

## Snapshots

```php
$snapshot = Helix::createSnapshot('my-index-name');
Helix::restoreSnapshot('restored-index', $snapshot->path, 1536);
Helix::deleteSnapshot($snapshot);
```

**Snapshot methods**

- `createSnapshot(Index|string $index): Snapshot`
- `restoreSnapshot(string $name, string $zipPath, int $dimension = 1536): Index`
- `deleteSnapshot(Snapshot $snapshot): void`
