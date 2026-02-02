@props([
    "title" => "",
    "value" => "",
    "helper" => null,
    "helperVariant" => "slate",
])

@php
    $helperClasses = [
        "emerald" => "text-green-600 dark:text-emerald-400",
        "amber" => "text-amber-600 dark:text-amber-300",
        "slate" => "text-slate-500 dark:text-slate-400",
    ];
@endphp

<article
    class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900/50"
>
    <div class="flex items-center justify-between gap-2">
        <div>
            <p
                class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400"
            >
                {{ $title }}
            </p>
            <p class="mt-2 text-2xl font-semibold text-indigo-600 dark:text-indigo-300">
                {{ $value }}
            </p>
        </div>
        <div
            class="rounded bg-indigo-50 text-indigo-600 size-8 flex items-center justify-center dark:bg-indigo-500/10 dark:text-indigo-300"
        >
            {{ $icon ?? "" }}
        </div>
    </div>
    @if ($helper)
        <p
            class="mt-2 text-xs {{ $helperClasses[$helperVariant] ?? $helperClasses["slate"] }}"
        >
            {{ $helper }}
        </p>
    @endif
</article>
