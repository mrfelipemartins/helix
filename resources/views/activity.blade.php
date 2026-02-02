<div class="flex flex-col gap-6 px-4 py-6 lg:px-6 lg:py-8 w-full">
    <header class="space-y-1">
        <h1
            class="text-lg font-semibold tracking-tight text-slate-900 dark:text-slate-50 sm:text-xl"
        >
            Activity &amp; Logs
        </h1>
        <p class="text-sm text-slate-600 dark:text-slate-300">
            Inspect recent operations, background jobs, and errors across
            Helix.
        </p>
    </header>
    <section class="rounded-lg border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-900/40">
        <div
            class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
        >
            <div
                class="flex flex-wrap items-center gap-2 text-xs text-slate-600 dark:text-slate-300"
            >
                <select
                    wire:model.live="index"
                    class="h-8 rounded border border-slate-200 bg-white px-2 text-xs text-slate-900 focus:border-indigo-600 focus:outline-none focus:ring-1 focus:ring-indigo-600 dark:border-slate-800 dark:bg-slate-900/80 dark:text-slate-100 dark:focus:border-indigo-500 dark:focus:ring-indigo-500"
                >
                    <option value="">All indexes</option>
                    @foreach (\MrFelipeMartins\Helix\Models\Index::pluck("name") as $idx)
                        <option value="{{ $idx }}">{{ $idx }}</option>
                    @endforeach
                </select>
                <select
                    wire:model.live="level"
                    class="h-8 rounded border border-slate-200 bg-white px-2 text-xs text-slate-900 focus:border-indigo-600 focus:outline-none focus:ring-1 focus:ring-indigo-600 dark:border-slate-800 dark:bg-slate-900/80 dark:text-slate-100 dark:focus:border-indigo-500 dark:focus:ring-indigo-500"
                >
                    <option value="">All levels</option>
                    <option value="info">Info</option>
                    <option value="warn">Warning</option>
                    <option value="error">Error</option>
                </select>
                <select
                    wire:model.live="type"
                    class="h-8 rounded border border-slate-200 bg-white px-2 text-xs text-slate-900 focus:border-indigo-600 focus:outline-none focus:ring-1 focus:ring-indigo-600 dark:border-slate-800 dark:bg-slate-900/80 dark:text-slate-100 dark:focus:border-indigo-500 dark:focus:ring-indigo-500"
                >
                    <option value="">All types</option>
                    <option value="insert">Insert</option>
                    <option value="search">Search</option>
                    <option value="create">Create</option>
                    <option value="drop">Drop</option>
                    <option value="maintenance">Maintenance</option>
                    <option value="delete">Delete</option>
                </select>
            </div>
            <div class="flex flex-wrap items-center gap-2 text-xs">
                <input
                    wire:model.live.debounce.300ms="search"
                    type="search"
                    placeholder="Search logs..."
                    class="h-8 w-52 rounded border border-slate-200 bg-white px-2 text-xs text-slate-900 placeholder:text-slate-400 focus:border-indigo-600 focus:outline-none focus:ring-1 focus:ring-indigo-600 dark:border-slate-800 dark:bg-slate-900/80 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:border-indigo-500 dark:focus:ring-indigo-500"
                />
            </div>
        </div>
        <div
            class="mt-3 overflow-hidden rounded border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-950/60"
        >
            <div
                class="grid grid-cols-[minmax(0,1.4fr)_minmax(0,1.4fr)_minmax(0,1.5fr)_minmax(0,2fr)_minmax(0,1fr)] items-center border-b border-slate-200 bg-slate-100 px-3 py-2 text-[11px] font-medium uppercase tracking-wide text-slate-600 dark:border-slate-900 dark:bg-slate-900/80 dark:text-slate-400"
            >
                <div>When</div>
                <div>Index</div>
                <div>Type</div>
                <div>Message</div>
                <div class="text-right">Meta</div>
            </div>
            <div class="divide-y divide-slate-200 text-xs text-slate-700 dark:divide-slate-900 dark:text-slate-200">
                @forelse ($this->activities as $activity)
                    <div
                        class="grid grid-cols-[minmax(0,1.4fr)_minmax(0,1.4fr)_minmax(0,1.5fr)_minmax(0,2fr)_minmax(0,1fr)] items-start px-3 py-2.5 hover:bg-slate-50 dark:hover:bg-slate-900/70"
                    >
                        <div>
                            <div class="font-mono text-[11px] text-slate-700 dark:text-slate-200">
                                {{ $activity->created_at }}
                            </div>
                            <div class="text-[11px] text-slate-500 dark:text-slate-400">
                                {{ $activity->created_at->diffForHumans() }}
                            </div>
                        </div>
                        <div class="text-slate-900 dark:text-slate-100">
                            {{ $activity->index }}
                        </div>
                        <div class="flex items-center gap-1">
                            <x-helix::badge
                                :variant="$activity->level->variant()">
                                {{ ucfirst($activity->type->value) }}
                            </x-helix::badge>
                        </div>
                        <div>
                            <p class="text-slate-900 dark:text-slate-50">
                                {{ $activity->message }}
                            </p>
                            @if ($activity->meta)
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">
                                    {{ json_encode($activity->meta) }}
                                </p>
                            @endif
                        </div>
                        <div class="text-right text-[11px] text-slate-500 dark:text-slate-400">
                            @if ($activity->meta && isset($activity->meta["id"]))
                                    id: {{ $activity->meta["id"] }}
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="px-3 py-3 text-center text-sm text-slate-500 dark:text-slate-400">
                        No activity found.
                    </div>
                @endforelse
            </div>
            <div class="px-3 py-2">
                {{ $this->activities->links("helix::components.pagination") }}
            </div>
        </div>
    </section>
</div>
