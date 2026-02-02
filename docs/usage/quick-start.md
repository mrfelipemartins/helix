---
title: Quick Start
---

# Quick Start

This guide walks you through a minimal but practical setup: create an index, insert vectors with metadata, and run searches and recommendations.

## 1) Create an index

Pick a dimension that matches your embeddings (for example, 1536 for many OpenAI models).

```php
use Illuminate\Support\Str;
use MrFelipeMartins\Helix\Facades\Helix;

$index = Helix::createIndex('my-index-name', 1536);
```

## 2) Insert vectors

Store the embedding along with any metadata you want to filter on later.

```php
Helix::insert('my-index-name', (string) Str::uuid(), $embedding, [
    'document_id' => 1,
    'text' => 'Vector databases are optimized for similarity search.',
]);
```

## 3) Search (direct call)

If you just need the top-N results, use the direct search API.

```php
$results = Helix::search(index: 'my-index-name', vector: $embedding, limit: 5);
```

## 4) Search (builder)

Use the builder when you need filtering, offsets, score thresholds, or custom metadata.

```php
$results = Helix::search()
    ->on('my-index-name')
    ->query($vector)
    ->where('document_id', 1)
    ->scoreThreshold(0.85)
    ->limit(5)
    ->get();
```

Tip: filters are applied after the ANN search (in memory). For heavy filtering, prefer engine-level filters when available.

## Recommendations

Recommendations use positive and negative examples and produce a new query vector.

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

## Next steps

- Review the Search and Recommendations pages for more examples.
- Configure storage paths and activity logging in the Configuration page.
- Use the Dashboard (`/helix` by default) to inspect indexes, records, and snapshots.
