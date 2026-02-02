---
title: Installation
---

# Installation

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
