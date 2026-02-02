@props([
    "href" => "#",
    "name",
    "engine",
    "dimension",
    "status",
    "statusVariant" => "slate",
    "size",
    "created",
    "tags" => [],
    
])

@php
    $dot =
        [
            "emerald" => "bg-green-600 dark:bg-emerald-400",
            "amber" => "bg-amber-600 dark:bg-amber-400",
            "slate" => "bg-slate-400 dark:bg-slate-400",
            "rose" => "bg-red-600 dark:bg-red-400",
            "indigo" => "bg-indigo-600 dark:bg-indigo-400",
        ][$statusVariant] ?? "bg-slate-400";
@endphp

<a
    href="{{ $href }}"
    {{ $attributes->merge(["class" => "grid grid-cols-[minmax(0,2fr)_minmax(0,1.4fr)_minmax(0,1.4fr)_minmax(0,1.1fr)_minmax(0,1.3fr)_minmax(0,1.4fr)_minmax(0,1.4fr)] items-center px-3 py-2.5 text-slate-700 hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-900/80"]) }}
>
    <div class="flex items-center gap-2">
        <span class="h-1.5 w-1.5 rounded-full {{ $dot }}"></span>
        <span class="font-medium text-slate-900 dark:text-slate-50">{{ $name }}</span>
        @foreach ($tags as $tag)
            <span
                class="rounded-full bg-slate-100 px-1.5 py-0.5 text-[10px] font-medium text-slate-600 dark:bg-slate-800 dark:text-slate-400"
            >
                {{ $tag }}
            </span>
        @endforeach
    </div>
    <div>{{ $engine }}</div>
    <div>{{ $dimension }}</div>
    <div>
        <x-helix::badge :variant="$statusVariant">
            <span class="h-1.5 w-1.5 rounded-full {{ $dot }}"></span>
            <span>{{ $status }}</span>
        </x-helix::badge>
    </div>
    <div>{{ $size }}</div>
    <div>{{ $created }}</div>
    <div class="flex justify-end gap-1.5 text-[11px]">
        {{ $actions ?? "" }}
    </div>
</a>
