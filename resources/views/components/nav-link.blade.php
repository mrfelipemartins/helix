@props([
    "href" => "#",
    "active" => false,
])

@php
    $classes = $active
        ? "relative flex items-center gap-1 text-slate-900 dark:text-slate-50"
        : "flex items-center gap-1 text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-slate-50";
@endphp

<a href="{{ $href }}" {{ $attributes->merge(["class" => $classes]) }}>
    <span>{{ $slot }}</span>
    @if ($active)
        <span
            class="absolute -bottom-2 left-0 right-0 h-0.5 rounded-full bg-indigo-600 dark:bg-indigo-500"
        ></span>
    @endif
</a>
