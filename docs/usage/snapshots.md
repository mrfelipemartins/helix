---
title: Snapshots
---

# Snapshots

Snapshots are zip archives of an index. They are useful for backups, migrations, and moving indexes between environments.

## Create a snapshot

```php
$snapshot = Helix::createSnapshot('my-index-name');
Helix::restoreSnapshot('restored-index', $snapshot->path, 1536);
Helix::deleteSnapshot($snapshot);
```

Snapshots are stored as zip archives on the configured snapshot disk.

## Restore a snapshot

Restoring creates a new index entry and extracts the zip into the target index path.

```php
$snapshot = Helix::createSnapshot('my-index-name');

Helix::restoreSnapshot(
    name: 'restored-index',
    zipPath: $snapshot->path,
    dimension: 1536
);
```

## Delete a snapshot

```php
Helix::deleteSnapshot($snapshot);
```

## Storage layout

By default, snapshots live under:

```
storage/helix/snapshots/<index_id>/YYYYmmdd_HHMMSS.zip
```

You can change the disk and root path via `HELIX_SNAPSHOT_DISK` and `HELIX_SNAPSHOT_ROOT`.

## Common workflows

- Backup before re-indexing a large dataset.
- Move a production index into staging for debugging.
- Version an index after a schema or embedding change.
