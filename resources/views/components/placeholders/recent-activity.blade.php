<article class="rounded-lg border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-900/40">
    <header class="flex items-center justify-between gap-3">
        <div>
            <h2 class="text-sm font-semibold tracking-tight text-slate-900 dark:text-slate-100">
                Recent Activity
            </h2>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                Latest operations across all stores.
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
        @for ($i = 0; $i < 5; $i++)
            <li
                class="flex items-center justify-between gap-3 rounded border border-slate-200 bg-white px-3 py-2 dark:border-slate-800 dark:bg-slate-950/40"
            >
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                    <div class="space-y-2">
                        <div
                            class="h-[12px] w-[200px] bg-slate-200 rounded animate-pulse dark:bg-slate-600"
                        ></div>
                        <div
                            class="h-[12px] w-[120px] bg-slate-200 rounded animate-pulse dark:bg-slate-600"
                        ></div>
                    </div>
                </div>
                <div
                    class="h-[16px] w-[100px] bg-slate-200 rounded animate-pulse dark:bg-slate-600"
                ></div>
            </li>
        @endfor
    </ol>
</article>
