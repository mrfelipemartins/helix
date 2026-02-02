@props([
    "href" => null,
    "variant" => "outline",
    "size" => "sm",
    "type" => "button",
])

@php
    $tag = $href ? "a" : "button";

    $base = "inline-flex items-center gap-1.5 rounded font-medium text-xs transition focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-indigo-500 dark:focus:ring-offset-slate-950";

    $variants = [
        "primary" => "bg-indigo-600 text-white hover:bg-indigo-700 border border-indigo-600 shadow-sm dark:bg-indigo-500 dark:hover:bg-indigo-400 dark:border-indigo-400/70",
        "outline" => "border border-slate-200 text-slate-700 hover:border-slate-300 bg-white dark:border-slate-700 dark:text-slate-200 dark:hover:border-slate-500 dark:bg-slate-900/60",
        "ghost" => "border border-slate-200 bg-slate-100/60 text-slate-600 hover:text-slate-900 hover:border-slate-300 dark:border-slate-800 dark:bg-slate-900/40 dark:text-slate-300 dark:hover:text-slate-100 dark:hover:border-slate-600",
        "danger" => "border border-red-600 text-red-600 hover:border-red-700 hover:text-red-700 dark:border-red-500/60 dark:text-red-300 dark:hover:border-red-400 dark:hover:text-red-200",
    ];

    $sizes = [
        "sm" => "px-3 py-1.5",
        "xs" => "px-2 py-0.5 text-[11px]",
    ];

    $classes = implode(" ", [$base, $variants[$variant] ?? $variants["outline"], $sizes[$size] ?? $sizes["sm"]]);
@endphp

@if ($tag === "a")
    <a href="{{ $href }}" {{ $attributes->merge(["class" => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button
        type="{{ $type }}"
        {{ $attributes->merge(["class" => $classes]) }}
    >
        {{ $slot }}
    </button>
@endif
