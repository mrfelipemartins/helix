<div class="flex flex-col gap-6 px-4 py-6 lg:px-6 lg:py-8 w-full">
    <header
        class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center"
    >
        <div>
            <h1
                class="text-lg font-semibold tracking-tight text-slate-900 dark:text-slate-50 sm:text-xl"
            >
                {{ $index->name }} (ID: {{ $index->id }})
            </h1>
        </div>
        <div class="flex items-center gap-3">
            <x-helix::button wire:click="optimize" variant="outline">
                <span>
                    <svg
                        class="size-4"
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="lucide lucide-wrench-icon lucide-wrench"
                    >
                        <path
                            d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.106-3.105c.32-.322.863-.22.983.218a6 6 0 0 1-8.259 7.057l-7.91 7.91a1 1 0 0 1-2.999-3l7.91-7.91a6 6 0 0 1 7.057-8.259c.438.12.54.662.219.984z"
                        />
                    </svg>
                </span>
                <span>Optimize</span>
            </x-helix::button>
        </div>
    </header>

    <x-helix::tabs :$tabs active="records">
        <x-helix::tab-content name="records">
            <livewire:helix.index.records :$index />
        </x-helix::tab-content>

        <x-helix::tab-content name="info">
            <livewire:helix.index.info :$index />
        </x-helix::tab-content>

        <x-helix::tab-content name="snapshots">
            <livewire:helix.index.snapshots :$index />
        </x-helix::tab-content>

        <x-helix::tab-content name="visualize">
            <livewire:helix.index.visualize :$index />
        </x-helix::tab-content>
    </x-helix::tabs>
</div>
