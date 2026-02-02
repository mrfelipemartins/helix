@props([
    "title",
    "meta" => null,
    "time",
    "variant" => "slate",
])

@php
    $dot =
        [
            "emerald" => "bg-green-600 dark:bg-emerald-400",
            "amber" => "bg-amber-600 dark:bg-amber-400",
            "slate" => "bg-slate-400 dark:bg-slate-400",
        ][$variant] ?? "bg-slate-400";

    $bg =
        [
            "emerald" => "bg-white dark:bg-slate-950/60",
            "amber" => "bg-white dark:bg-slate-950/40",
            "slate" => "bg-white dark:bg-slate-950/40",
        ][$variant] ?? "bg-white dark:bg-slate-950/40";
@endphp

<li
    class="flex items-center justify-between gap-3 rounded border border-slate-200 dark:border-slate-800 {{ $bg }} px-3 py-2"
>
    <div class="flex items-center gap-2">
        <span class="h-1.5 w-1.5 rounded-full {{ $dot }}"></span>
        <div>
            <p class="font-medium text-slate-900 dark:text-slate-100">{{ $title }}</p>
            @if ($meta)
                <p class="text-[11px] text-slate-500 dark:text-slate-400">{{ $meta }}</p>
            @endif
        </div>
    </div>
    <span class="text-[11px] text-slate-500 dark:text-slate-400">{{ $time }}</span>
</li>
