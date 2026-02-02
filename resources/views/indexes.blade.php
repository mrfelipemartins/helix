<div class="flex flex-col gap-6 px-4 py-6 lg:px-6 lg:py-8 w-full">
    <header
        class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center"
    >
        <div>
            <h1
                class="text-lg font-semibold tracking-tight text-slate-900 dark:text-slate-50 sm:text-xl"
            >
                Indexes
            </h1>
            <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">
                Create, inspect, and manage your vector indexes.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <x-helix::button
                @click="$wire.showCreateModal = true"
                variant="primary"
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
            <x-helix::button
                @click="$wire.showUploadModal = true"
                variant="outline"
            >
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
                    class="lucide lucide-upload-icon lucide-upload"
                >
                    <path d="M12 3v12" />
                    <path d="m17 8-5-5-5 5" />
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                </svg>
                <span>Upload Snapshot</span>
            </x-helix::button>
        </div>
    </header>

    <section
        class="rounded-lg border border-slate-200 bg-white p-3 sm:p-4 dark:border-slate-800 dark:bg-slate-900/40"
    >
        <div
            class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
        >
            <div
                class="flex flex-wrap items-center gap-3 text-xs text-slate-600 dark:text-slate-300"
            >
                <div class="flex items-center gap-2">
                    <span
                        class="h-1.5 w-1.5 rounded-full bg-green-600 dark:bg-emerald-400"
                    ></span>
                    <span>Ready</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-amber-600 dark:bg-amber-400"></span>
                    <span>Optimizing</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-red-600 dark:bg-red-400"></span>
                    <span>Error</span>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2 text-xs">
                <input
                    type="search"
                    placeholder="Search by name..."
                    wire:model.live.debounce="search"
                    class="h-8 w-48 rounded border border-slate-200 bg-white px-2 text-xs text-slate-900 placeholder:text-slate-400 focus:border-indigo-600 focus:outline-none focus:ring-1 focus:ring-indigo-600 dark:border-slate-800 dark:bg-slate-900/80 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:border-indigo-500 dark:focus:ring-indigo-500 sm:w-64"
                />
            </div>
        </div>
        <div
            class="mt-3 overflow-hidden rounded border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-950/60"
        >
            <div
                class="hidden grid-cols-6 items-center border-b border-slate-200 bg-slate-100 px-3 py-2 text-[11px] font-medium uppercase tracking-wide text-slate-600 dark:border-slate-900 dark:bg-slate-900/80 dark:text-slate-400 sm:grid"
            >
                <div>Name</div>
                <div>Dimension</div>
                <div>Status</div>
                <div>Size</div>
                <div>Created</div>
                <div class="text-right">Actions</div>
            </div>
            <div class="divide-y divide-slate-200 text-xs text-slate-700 dark:divide-slate-900 dark:text-slate-200">
                @forelse ($this->indexes as $index)
                    <div class="block hover:bg-slate-50 dark:hover:bg-slate-900/70">
                        <div
                            class="grid grid-cols-1 gap-1 px-3 py-2.5 sm:grid-cols-6 sm:items-center"
                        >
                            <div class="flex items-center gap-2">
                                <span
                                    class="h-1.5 w-1.5 rounded-full bg-green-600 dark:bg-emerald-400"
                                ></span>
                                <span class="font-medium text-slate-900 dark:text-slate-50">
                                    {{ $index->name }}
                                </span>
                            </div>
                            <div class="hidden sm:block">
                                {{ $index->dimension }}
                            </div>
                            <div>
                                <x-helix::badge
                                    class="ml-1"
                                    :variant="$index->status->variant()"
                                    size="xs"
                                >
                                    <span class="capitalize">
                                        {{ $index->status->value }}
                                    </span>
                                </x-helix::badge>
                            </div>
                            <div>{{ $index->size }}</div>
                            <div>{{ $index->created_at }}</div>
                            <div class="flex justify-end gap-1.5 text-[11px]">
                                <div class="flex gap-1.5">
                                    <a
                                        wire:navigate
                                        href="{{ route("helix.indexes.show", ["index" => $index->id]) }}"
                                        class="rounded border border-slate-200 px-2 py-2 hover:border-slate-300 dark:border-slate-700 dark:hover:border-slate-500"
                                    >
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
                                                class="lucide lucide-eye-icon lucide-eye"
                                            >
                                                <path
                                                    d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"
                                                />
                                                <circle cx="12" cy="12" r="3" />
                                            </svg>
                                        </span>
                                    </a>
                                    <button
                                        type="button"
                                        wire:confirm="Are you sure you want to delete this index? This action cannot be undone."
                                        wire:click="deleteIndex({{ $index->id }})"
                                        class="rounded border border-red-600 px-2 py-2 text-red-600 hover:border-red-700 hover:text-red-700 dark:border-red-500/60 dark:text-red-300 dark:hover:border-red-400 dark:hover:text-red-200"
                                    >
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
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div>
                        <p
                            class="text-center font-medium select-none opacity-60 text-sm text-slate-500 py-4 dark:text-slate-400"
                        >
                            No indexes found.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <x-helix::modal title="Create Index" wire:model="showCreateModal">
        <form class="space-y-6" wire:submit="createIndex">
            <div class="space-y-2">
                <label
                    for="store-name"
                    class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                >
                    Index Name
                </label>
                <x-helix::input
                    id="store-name"
                    wire:model="form.name"
                    type="text"
                    placeholder="Enter store name"
                    class="w-full"
                />
            </div>
            <div class="space-y-2">
                <label
                    for="vector-dimension"
                    class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                >
                    Vector Dimension
                </label>
                <x-helix::input
                    id="vector-dimension"
                    wire:model="form.dimension"
                    type="number"
                    placeholder="e.g., 128, 256, 512"
                    class="w-full"
                />
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-800">
                <x-helix::button
                    type="button"
                    variant="outline"
                    x-on:click="open = false"
                >
                    Cancel
                </x-helix::button>
                <x-helix::button type="submit" variant="primary">
                    Create Index
                </x-helix::button>
            </div>
        </form>
    </x-helix::modal>

    <x-helix::modal title="Upload Snapshot" wire:model="showUploadModal">
        <form class="space-y-6" wire:submit="uploadSnapshot">
            <div class="space-y-2">
                <label
                    for="upload-name"
                    class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                >
                    Index Name
                </label>
                <x-helix::input
                    id="upload-name"
                    wire:model="uploadForm.name"
                    type="text"
                    placeholder="Enter index name"
                    class="w-full"
                />
                @error("uploadForm.name")
                    <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            <div class="space-y-2">
                <label
                    for="upload-file"
                    class="block text-sm font-medium text-slate-700 dark:text-slate-200"
                >
                    Snapshot File
                </label>
                <input
                    id="upload-file"
                    type="file"
                    wire:model="uploadForm.file"
                    class="block w-full text-sm text-slate-700 file:mr-4 file:rounded file:border-0 file:bg-indigo-600 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-white hover:file:bg-indigo-700 dark:text-slate-200 dark:file:bg-indigo-500 dark:hover:file:bg-indigo-400"
                />
                @error("uploadForm.file")
                    <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-800">
                <x-helix::button
                    type="button"
                    variant="outline"
                    x-on:click="open = false"
                >
                    Cancel
                </x-helix::button>
                <x-helix::button type="submit" variant="primary">
                    Upload
                </x-helix::button>
            </div>
        </form>
    </x-helix::modal>
</div>
