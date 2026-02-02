---
title: Semantic Search (Docs/KB)
---

# Semantic Search (Docs/Knowledge Base)

Build a searchable knowledge base by embedding content and storing it in Helix with metadata for filtering.

## 1) Index content

```php
use Illuminate\Support\Str;
use MrFelipeMartins\Helix\Facades\Helix;
use OpenAI\Laravel\Facades\OpenAI;

Helix::createIndex('kb', 1536);

foreach ($chunks as $chunk) {
    $embedding = OpenAI::embeddings()->create([
        'model' => 'text-embedding-3-small',
        'input' => $chunk['text'],
    ])->embeddings[0]->embedding;

    Helix::insert('kb', (string) Str::uuid(), $embedding, [
        'doc_id' => $chunk['doc_id'],
        'title' => $chunk['title'],
        'product' => $chunk['product'],
        'version' => $chunk['version'],
        'text' => $chunk['text'],
    ]);
}
```

## 2) Search with filters

```php
use MrFelipeMartins\Helix\Facades\Helix;
use OpenAI\Laravel\Facades\OpenAI;

$query = 'How do I reset my API key?';

$queryEmbedding = OpenAI::embeddings()->create([
    'model' => 'text-embedding-3-small',
    'input' => $query,
])->embeddings[0]->embedding;

$results = Helix::search()
    ->on('kb')
    ->query($queryEmbedding)
    ->where('product', 'acme-cloud')
    ->where('version', '>=', '2.1')
    ->limit(5)
    ->get();
```

## 3) Show results

```php
foreach ($results as $result) {
    $meta = $result['metadata'] ?? [];
    echo $meta['title'] ?? 'Untitled';
    echo \"\\n\";
}
```

## Tips

- Chunk your documents (e.g., 300–800 tokens).
- Store `title`, `url`, and `version` for better filters and links.
- Use a score threshold if results are noisy.
