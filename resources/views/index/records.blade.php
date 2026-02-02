<div class="flex flex-col gap-4">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-sm font-semibold tracking-tight text-slate-900 dark:text-slate-100">
                Records
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Browse and manage records stored in this index.
            </p>
        </div>
    </div>
    <div class="space-y-6">
        @forelse ($this->records as $record)
            <x-helix::card :title="'Record ID: ' . $record['id']">
                <x-slot:actions>
                    <x-helix::button variant="danger">
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
                                class="lucide lucide-trash-icon lucide-trash"
                            >
                                <path
                                    d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"
                                />
                                <path d="M3 6h18" />
                                <path
                                    d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"
                                />
                            </svg>
                        </span>
                    </x-helix::button>
                </x-slot>
                <div class="space-y-4">
                    <div>
                        <label class="font-medium text-slate-700 dark:text-slate-200">
                            Payload:
                        </label>
                        <div>
                            <x-helix::json :json="$record['metadata']"></x-helix::json>
                        </div>
                    </div>
                    <div>
                        <label class="font-medium text-slate-700 dark:text-slate-200">Length:</label>
                        <p class="mt-1 text-sm text-slate-700 dark:text-slate-100">
                            {{ count($record["vector"]) }}
                        </p>
                    </div>
                </div>
            </x-helix::card>
        @empty
            <p class="text-center text-sm text-slate-500 dark:text-slate-400">No records found.</p>
        @endforelse
    </div>
    <div class="mt-4">
        {{ $this->records->links("helix::components.pagination") }}
    </div>
</div>
