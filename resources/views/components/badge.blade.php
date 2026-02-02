@props([
    "variant" => "slate",
    "pill" => true,
])

@php
    $variants = [
        "slate" => "bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300",
        "emerald" => "bg-green-50 text-green-700 dark:bg-emerald-500/10 dark:text-emerald-300",
        "amber" => "bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-300",
        "indigo" => "bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-200",
        "rose" => "bg-red-50 text-red-700 dark:bg-red-500/10 dark:text-red-300",
    ];

    $dot =
        [
            "slate" => "bg-slate-400 dark:bg-slate-400",
            "emerald" => "bg-green-600 dark:bg-emerald-400",
            "amber" => "bg-amber-600 dark:bg-amber-400",
            "indigo" => "bg-indigo-600 dark:bg-indigo-400",
            "rose" => "bg-red-600 dark:bg-red-400",
        ][$variant] ?? "bg-slate-400";

    $classes = implode(" ", [
        "inline-flex items-center gap-1 text-[10px] font-medium px-2 py-0.5",
        $pill ? "rounded-full" : "rounded",
        $variants[$variant] ?? $variants["slate"],
    ]);
@endphp

<span {{ $attributes->merge(["class" => $classes]) }}>
    <span class="h-1.5 w-1.5 rounded-full {{ $dot }}"></span>
    {{ $slot }}
</span>
