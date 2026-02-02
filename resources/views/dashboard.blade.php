<div class="flex flex-col gap-6 px-4 py-6 lg:px-6 lg:py-8 w-full">
    <header
        class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center"
    >
        <div>
            <h1
                class="text-lg font-semibold tracking-tight text-slate-900 dark:text-slate-50 sm:text-xl"
            >
                Dashboard
            </h1>
            <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">
                Overview of your vector indexes, utilization, and activity.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <x-helix::button
                variant="primary"
                :href="route('helix.indexes', ['showCreateModal' => 'true'])"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="size-4"
                    width="24"
                    height="24"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="lucide lucide-plus-icon lucide-plus"
                >
                    <path d="M5 12h14" />
                    <path d="M12 5v14" />
                </svg>
                <span>Create Index</span>
            </x-helix::button>
        </div>
    </header>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <livewire:helix.metrics.total-indexes />
        <livewire:helix.metrics.total-records />
        <livewire:helix.metrics.active-indexes />
        <livewire:helix.metrics.disk-usage />
    </section>

    @if(config('helix.activity.enabled'))
        <section class="mt-4">
            <livewire:helix.recent-activity />
        </section>
    @endif
</div>
