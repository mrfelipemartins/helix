<div class="flex flex-col gap-4">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-sm font-semibold tracking-tight text-slate-900 dark:text-slate-100">
                Visualize
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                2D visualization of record distribution in this index.
            </p>
        </div>
    </div>
    <div
        x-data="visualize({{ json_encode($this->points) }})"
        class="w-full"
    >
        <x-helix::card class="p-4" title="Record Distribution 2D">
            <canvas x-ref="canvas" class="w-full" height="320"></canvas>
        </x-helix::card>
    </div>
</div>
