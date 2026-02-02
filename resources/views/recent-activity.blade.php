<article class="rounded-lg border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-900/40">
    <header class="flex items-center justify-between gap-3">
        <div>
            <h2 class="text-sm font-semibold tracking-tight text-slate-900 dark:text-slate-100">
                Recent Activity
            </h2>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                Latest operations across all indexes.
            </p>
        </div>
        <x-helix::button
            :href="route('helix.activity')"
            wire:navigate
            variant="ghost"
            size="xs"
        >
            View logs
        </x-helix::button>
    </header>
    <ol class="mt-3 space-y-2 text-xs text-slate-600 dark:text-slate-200">
        @forelse ($this->activities as $activity)
            <x-helix::activity-item
                :title="$activity->message"
                :meta="''"
                :time="$activity->created_at->diffForHumans()"
                :variant="$activity->level->variant()"
            />
        @empty
            <div>
                <p
                    class="text-center font-medium select-none opacity-60 text-sm text-slate-500 py-4 dark:text-slate-400"
                >
                    No recent activity.
                </p>
            </div>
        @endforelse
    </ol>
</article>
