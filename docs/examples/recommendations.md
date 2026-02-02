---
title: Recommendations (More Like This)
---

# Recommendations (More Like This)

Use Helix to power “more like this” recommendations for content or products.

## 1) Index items

```php
use Illuminate\Support\Str;
use MrFelipeMartins\Helix\Facades\Helix;
use OpenAI\Laravel\Facades\OpenAI;

Helix::createIndex('items', 1536);

foreach ($items as $item) {
    $embedding = OpenAI::embeddings()->create([
        'model' => 'text-embedding-3-small',
        'input' => $item['text'],
    ])->embeddings[0]->embedding;

    Helix::insert('items', (string) Str::uuid(), $embedding, [
        'item_id' => $item['id'],
        'category' => $item['category'],
        'title' => $item['title'],
    ]);
}
```

## 2) Recommend similar items

```php
$results = Helix::recommend()
    ->on('items')
    ->positiveIds(['item-123'])
    ->limit(6)
    ->get();
```

## 3) Add negative examples

Exclude items that a user dislikes or already saw.

```php
$results = Helix::recommend()
    ->on('items')
    ->positiveIds(['item-123'])
    ->negativeIds(['item-999'])
    ->limit(6)
    ->get();
```

## 4) Filter by metadata

```php
$results = Helix::recommend()
    ->on('items')
    ->positiveIds(['item-123'])
    ->where('category', 'news')
    ->limit(6)
    ->get();
```

## Tips

- Store metadata like `category`, `price`, or `tags` to filter results.
- Use multiple positive IDs for a better “taste profile.”
