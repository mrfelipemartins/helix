---
title: Dashboard
---

# Dashboard

The dashboard is available at `/<HELIX_PATH>` (default: `/helix`). It helps you inspect indexes, browse records, review activity, and manage snapshots.

## What you can do

- View all indexes and their status.
- Inspect index stats and disk usage.
- Browse and paginate records.
- Create, restore, and delete snapshots.
- Visualize records (2D projection of the first two dimensions).

## Access control

Define the gate in your app:

```php
use Illuminate\Support\Facades\Gate;

Gate::define('viewHelix', fn ($user) => $user?->isAdmin());
```

Notes:

- By default, Helix defines `viewHelix` to allow access only in the `local` environment.
- You can disable the gate with `HELIX_GATE=false` and/or customize middleware.

## Middleware

The dashboard uses the middleware list from `helix.middleware` (default: `['web']`). To add auth:

```php
// config/helix.php
'middleware' => ['web', 'auth'],
```

## Route path

Change the path with:

```
HELIX_PATH=admin/helix
```

The dashboard will then be available at `/admin/helix`.
