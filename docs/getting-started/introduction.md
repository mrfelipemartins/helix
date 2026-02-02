---
title: Introduction
---

# Helix

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

## Use cases

- Semantic search over documents
- Recommendations and similarity matching
- RAG / embedding-based retrieval

## Under the hood

Helix aims to support multiple storage/index drivers over time. Today, the supported driver is [**centamiv/vektor**](https://github.com/centamiv/vektor).

Vektor is a high-performance, file-based, embedded vector database written in native PHP. It is designed for zero-RAM overhead by reading data directly from disk and uses strict binary file layouts plus optimized disk-seeking strategies to perform ANN searches with the HNSW algorithm. It supports cosine similarity (configurable), uses file locking for safe concurrent access.
