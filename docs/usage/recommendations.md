---
title: Recommendations
---

# Recommendations

Recommendations use the average-vector strategy to build a new query vector. You can provide IDs, vectors, or both.

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

## Behavior

- At least one positive example is required.
- IDs are resolved from the index.

## Real-world examples

### Content recommendations

Suggest articles similar to a reader’s interests while excluding a disliked piece.

```php
$results = Helix::recommend()
    ->on('articles')
    ->positiveIds(['article-123', 'article-456'])
    ->negativeIds(['article-999'])
    ->limit(6)
    ->get();
```

### Product discovery

Recommend items related to a customer’s recent views.

```php
$results = Helix::recommend()
    ->on('products')
    ->positiveIds(['sku-1001', 'sku-1002'])
    ->limit(8)
    ->get();
```

### People search

Find candidates similar to a short list of successful hires.

```php
$results = Helix::recommend()
    ->on('candidates')
    ->positiveIds(['candidate-10', 'candidate-42'])
    ->limit(10)
    ->get();
```

### Custom vectors (no stored IDs)

Use raw vectors directly when you don’t have stored IDs.

```php
$results = Helix::recommend()
    ->on('documents')
    ->positiveVectors([$vectorA, $vectorB])
    ->negativeVectors([$vectorC])
    ->limit(5)
    ->get();
```

## Use cases

- “More like this” recommendations on content pages.
- Similar product suggestions in e-commerce.
- Personalized feeds based on a user’s liked items.
- Lead or candidate matching using profile embeddings.
