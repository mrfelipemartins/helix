---
title: Search
---

# Search

Helix supports direct search calls and a fluent builder for richer queries. The search returns the closest items to a query vector using ANN (Approximate Nearest Neighbor) search.

## Direct search (simple)

```php
$results = Helix::search(
    index: 'my-index-name',
    vector: $embedding,
    limit: 10
);
```

Use this for the simplest case: “give me the top N results.”

## Builder search (advanced)

The builder lets you add filters, offsets, and score thresholds.

```php
$results = Helix::search()
    ->on('my-index-name')
    ->query($embedding)
    ->limit(10)
    ->get();
```

## Filters (metadata)

Filters are applied after the ANN search (in memory), so Helix will over-fetch results and then apply filters.

```php
$results = Helix::search()
    ->on('my-index-name')
    ->query($embedding)
    ->where('author.id', 123)
    ->whereBetween('score', [0.2, 0.8])
    ->whereNotNull('summary')
    ->limit(10)
    ->get();
```

### Common filter helpers

```php
->where('status', 'published')
->whereIn('category', ['news', 'docs'])
->whereNotNull('summary')
->whereBetween('price', [10, 50])
```

## Offsets and oversampling

When you set offsets or filters, Helix fetches extra results to avoid empty pages.

```php
$results = Helix::search()
    ->on('my-index-name')
    ->query($embedding)
    ->offset(10)
    ->oversample(50)
    ->limit(10)
    ->get();
```

## Score threshold

Filter out low-confidence matches using a minimum similarity score.

```php
$results = Helix::search()
    ->on('my-index-name')
    ->query($embedding)
    ->scoreThreshold(0.85)
    ->limit(10)
    ->get();
```

## Pagination

To list raw records without ANN search:

```php
$paginator = Helix::list('my-index-name', page: 1, perPage: 50);
```

## What the results look like

Each item includes an `id` and usually a `score`. If metadata was stored, it is returned as `metadata`.

```php
[
    [
        'id' => 'doc-1',
        'score' => 0.92,
        'metadata' => [
            'document_id' => 1,
            'title' => 'Vector databases are optimized for similarity search.',
        ],
    ],
]
```
