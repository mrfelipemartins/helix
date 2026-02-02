<div class="flex flex-col gap-4">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-sm font-semibold tracking-tight text-slate-900 dark:text-slate-100">
                Snapshots
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Create, download, and delete snapshots for this index.
            </p>
        </div>
        <x-helix::button
            wire:click="createSnapshot"
            variant="primary"
            size="sm"
        >
            Create Snapshot
        </x-helix::button>
    </div>

    <div
        class="overflow-hidden rounded border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-950/60"
    >
        <div
            class="hidden grid-cols-5 items-center border-b border-slate-200 bg-slate-100 px-3 py-2 text-[11px] font-medium uppercase tracking-wide text-slate-600 dark:border-slate-900 dark:bg-slate-900/80 dark:text-slate-400 sm:grid"
        >
            <div>Name</div>
            <div>Path</div>
            <div>Size</div>
            <div>Created</div>
            <div class="text-right">Actions</div>
        </div>
        <div class="divide-y divide-slate-200 text-xs text-slate-700 dark:divide-slate-900 dark:text-slate-200">
            @forelse ($this->snapshots as $snapshot)
                <div
                    class="grid grid-cols-1 gap-2 px-3 py-2 hover:bg-slate-50 dark:hover:bg-slate-900/70 sm:grid-cols-5 sm:items-center"
                >
                    <div class="font-mono text-[12px] text-slate-900 dark:text-slate-100">
                        {{ $snapshot->name }}
                    </div>
                    <div
                        class="truncate text-[11px] text-slate-500 dark:text-slate-400 sm:col-span-1"
                    >
                        {{ $snapshot->path }}
                    </div>
                    <div class="text-[11px] text-slate-600 dark:text-slate-300">
                        {{ \MrFelipeMartins\Helix\Support\Helpers::formatBytes($snapshot->size) }}
                    </div>
                    <div class="text-[11px] text-slate-500 dark:text-slate-400">
                        {{ $snapshot->created_at }}
                    </div>
                    <div class="flex justify-end gap-2 text-[11px]">
                        <x-helix::button
                            href="{{ route('helix.snapshots.download', $snapshot) }}"
                            variant="outline"
                            size="xs"
                        >
                            Download
                        </x-helix::button>
                        <x-helix::button
                            wire:click="deleteSnapshot({{ $snapshot->id }})"
                            variant="danger"
                            wire:confirm="Are you sure you want to delete this snapshot?"
                            size="xs"
                        >
                            Delete
                        </x-helix::button>
                    </div>
                </div>
            @empty
                <div class="px-3 py-3 text-center text-sm text-slate-500 dark:text-slate-400">
                    No snapshots yet.
                </div>
            @endforelse
        </div>
    </div>
</div>
