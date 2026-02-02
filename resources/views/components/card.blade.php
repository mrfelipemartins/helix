<div class="rounded-lg border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-900/40">
    <header class="flex items-center justify-between gap-3">
        <div>
            <h2 class="text-sm font-semibold tracking-tight text-slate-900 dark:text-slate-100">
                {{ $title }}
            </h2>
            @isset($description)
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                    {{ $description }}
                </p>
            @endisset
        </div>
        @isset($actions)
            <div class="flex items-center gap-2">
                {{ $actions }}
            </div>
        @endisset
    </header>
    <div class="mt-3 space-y-2 text-xs text-slate-700 dark:text-slate-200">
        {{ $slot }}
    </div>
</div>
