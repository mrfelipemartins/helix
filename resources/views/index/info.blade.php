<div class="flex flex-col gap-4">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-sm font-semibold tracking-tight text-slate-900 dark:text-slate-100">
                Info
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Detailed information and statistics about this index.
            </p>
        </div>
    </div>
    <x-helix::card title="Index Info">
        <x-helix::json :json="$this->stats ?? []" :collapsed="false" />
    </x-helix::card>
</div>
