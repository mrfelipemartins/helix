@props([
    "tabs" => [],
    "active" => null,
])

@php
    $activeKey = $active ?? (collect($tabs)->first()["key"] ?? null);
@endphp

<div x-data="{ active: '{{ $activeKey }}' }" class="w-full">
    <div class="hidden sm:block">
        <nav
            class="-mb-px flex space-x-4 border-b border-slate-200 dark:border-slate-800"
            aria-label="Tabs"
        >
            @foreach ($tabs as $tab)
                <button
                    type="button"
                    class="whitespace-nowrap border-b-2 px-1 py-3 text-sm font-medium"
                    :class="active === '{{ $tab["key"] }}' ? 'border-indigo-600 text-indigo-700 dark:border-indigo-500 dark:text-indigo-200' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-900 dark:text-slate-400 dark:hover:border-slate-600 dark:hover:text-slate-200'"
                    @click="active = '{{ $tab["key"] }}'"
                >
                    {{ $tab["label"] }}
                </button>
            @endforeach
        </nav>
    </div>

    <div class="grid grid-cols-1 sm:hidden">
        <select
            x-model="active"
            aria-label="Select a tab"
            class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white py-2 pl-3 pr-8 text-base text-slate-900 outline outline-1 outline-slate-200 focus:outline focus:outline-2 focus:outline-indigo-600 dark:bg-slate-900/70 dark:text-slate-100 dark:outline-slate-700 dark:focus:outline-indigo-500"
        >
            @foreach ($tabs as $tab)
                <option value="{{ $tab["key"] }}">{{ $tab["label"] }}</option>
            @endforeach
        </select>
        <svg
            viewBox="0 0 16 16"
            class="pointer-events-none col-start-1 row-start-1 mr-2 h-4 w-4 self-center justify-self-end text-slate-500 dark:text-slate-500"
            aria-hidden="true"
        >
            <path
                d="M4.22 6.22a.75.75 0 0 1 1.06 0L8 8.94l2.72-2.72a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L4.22 7.28a.75.75 0 0 1 0-1.06Z"
                clip-rule="evenodd"
                fill-rule="evenodd"
            />
        </svg>
    </div>

    <div class="mt-4">
        {{ $slot }}
    </div>
</div>
