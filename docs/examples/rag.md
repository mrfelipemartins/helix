---
title: RAG (Laravel OpenAI)
---

# RAG with Helix + Laravel OpenAI

This example shows a simple Retrieval-Augmented Generation (RAG) flow:

1. Embed a query with Laravel OpenAI.
2. Retrieve the most relevant chunks from Helix.
3. Provide those chunks as context to the chat model.

## Requirements

- `mrfelipemartins/helix`
- `openai-php/laravel` (Laravel OpenAI)

## 1) Index your documents (once)

Embed your documents in chunks and store them in Helix.

```php
use Illuminate\Support\Str;
use MrFelipeMartins\Helix\Facades\Helix;
use OpenAI\Laravel\Facades\OpenAI;

$index = Helix::createIndex('docs', 1536);

foreach ($chunks as $chunk) {
    $embedding = OpenAI::embeddings()->create([
        'model' => 'text-embedding-3-small',
        'input' => $chunk['text'],
    ])->embeddings[0]->embedding;

    Helix::insert('docs', (string) Str::uuid(), $embedding, [
        'document_id' => $chunk['document_id'],
        'text' => $chunk['text'],
        'source' => $chunk['source'],
    ]);
}
```

## 2) Retrieve context for a query

```php
use MrFelipeMartins\Helix\Facades\Helix;
use OpenAI\Laravel\Facades\OpenAI;

$query = 'How do I restore a snapshot?';

$queryEmbedding = OpenAI::embeddings()->create([
    'model' => 'text-embedding-3-small',
    'input' => $query,
])->embeddings[0]->embedding;

$results = Helix::search(index: 'docs', vector: $queryEmbedding, limit: 5);

$context = collect($results)
    ->map(fn ($item) => $item['metadata']['text'] ?? '')
    ->filter()
    ->implode("\n\n");
```

## 3) Generate the answer

```php
use OpenAI\Laravel\Facades\OpenAI;

$response = OpenAI::chat()->create([
    'model' => 'gpt-4o-mini',
    'messages' => [
        [
            'role' => 'system',
            'content' => 'You are a helpful assistant. Use the provided context to answer.',
        ],
        [
            'role' => 'user',
            'content' => "Question: {$query}\n\nContext:\n{$context}",
        ],
    ],
]);

$answer = $response->choices[0]->message->content;
```

## Tips

- Store chunk metadata (document id, title, URL) so you can cite sources.
- Tune the `limit` based on your model’s context size.
- Add a score threshold to filter low-confidence matches.
