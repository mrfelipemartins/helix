@props([
    "title" => null,
    "size" => "md",
    "dismissible" => true,
])

@php
    if (($wireModel = $attributes->wire("model")) && $wireModel->directive && ! $wireModel->hasModifier("self")) {
        unset($attributes[$wireModel->directive]);
        $wireModel->directive .= ".self";
        $attributes = $attributes->merge([$wireModel->directive => $wireModel->value]);
    }

    $entangle = $wireModel ? Blade::render('@entangle(\'' . $wireModel->value() . '\')') : "false";

    $sizes = [
        "sm" => "sm:max-w-sm",
        "md" => "sm:max-w-lg",
        "lg" => "sm:max-w-2xl",
        "xl" => "sm:max-w-4xl",
    ];

    $panelClasses = $sizes[$size] ?? $sizes["md"];
@endphp

<div
    x-data="{ open: {{ $entangle }} }"
    x-show="open"
    x-on:keydown.escape.window="open = false"
    class="fixed inset-0 z-50 flex items-center justify-center px-4 sm:px-6"
    aria-modal="true"
    role="dialog"
>
    <div
        x-show="open"
        x-transition.opacity.duration.150ms
        class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm dark:bg-slate-900/70"
        @if($dismissible) x-on:click="open = false" @endif
    ></div>

    <div
        x-show="open"
        x-transition.duration.200ms
        x-transition.scale.origin.bottom
        class="relative w-full {{ $panelClasses }} rounded-xl border border-slate-200 bg-white shadow-2xl dark:border-slate-800 dark:bg-slate-950/90"
    >
        <div
            class="flex items-start justify-between border-b border-slate-200 px-4 py-3 sm:px-5 dark:border-slate-800"
        >
            <h2 class="text-sm font-semibold leading-6 text-slate-900 dark:text-slate-100">
                {{ $title }}
            </h2>
            <button
                type="button"
                class="rounded p-1 text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200"
                x-on:click="open = false"
            >
                <span class="sr-only">Close</span>
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M18 6 6 18" />
                    <path d="m6 6 12 12" />
                </svg>
            </button>
        </div>

        <div class="px-4 py-4 sm:px-5">
            {{ $slot }}
        </div>
    </div>
</div>
