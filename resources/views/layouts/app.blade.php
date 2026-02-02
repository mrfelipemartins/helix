@use("MrFelipeMartins\Helix\Facades\Helix")
<!DOCTYPE html>
<html lang="{{ str_replace("_", "-", app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <title>Helix</title>

        <link rel="preconnect" href="https://fonts.bunny.net" />
        <link
            href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap"
            rel="stylesheet"
        />
        <link
            href="data:image/png;base64,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"
            rel="icon"
            type="image/x-icon"
        />

        {!! Helix::css() !!}
        @livewireStyles
        @livewireScriptConfig
    </head>
    <body class="min-h-screen bg-slate-50 text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
        <div class="flex min-h-screen flex-col">
            <header
                class="border-b border-slate-200 bg-white/90 backdrop-blur dark:border-slate-800 dark:bg-slate-950/80"
            >
                <div
                    class="mx-auto grid max-w-6xl grid-cols-3 items-center px-4 py-3 lg:px-6"
                >
                    <div class="flex items-center gap-8">
                        <a
                            href="{{ route("helix.dashboard") }}"
                            wire:navigate
                            class="flex items-center gap-2"
                        >
                            <x-helix::logo class="h-8 w-8"></x-helix::logo>
                            <span
                                class="text-xl font-semibold tracking-tight text-slate-900 dark:text-slate-50"
                            >
                                Helix
                            </span>
                        </a>
                    </div>
                    <nav
                        class="hidden justify-center gap-5 text-sm font-medium text-slate-600 dark:text-slate-300 sm:flex"
                    >
                        <x-helix::nav-link
                            href="{{route('helix.dashboard')}}"
                            :active="request()->routeIs('helix.dashboard')"
                            wire:navigate
                        >
                            Dashboard
                        </x-helix::nav-link>
                        <x-helix::nav-link
                            href="{{route('helix.indexes')}}"
                            :active="request()->routeIs('helix.indexes*')"
                            wire:navigate
                        >
                            Indexes
                        </x-helix::nav-link>
                        @if(config('helix.activity.enabled'))
                            <x-helix::nav-link
                                href="{{route('helix.activity')}}"
                                :active="request()->routeIs('helix.activity')"
                                wire:navigate
                            >
                                Activity
                            </x-helix::nav-link>
                        @endif
                    </nav>

                    <div class="flex justify-end gap-4">
                        <x-helix::badge
                            class="hidden sm:inline-flex border border-green-600/20 dark:border-emerald-500/30"
                            variant="emerald"
                        >
                            <span class="capitalize">
                                {{ app()->environment() }}
                            </span>
                        </x-helix::badge>

                        <x-helix::theme />
                    </div>
                </div>
            </header>
            <main class="mx-auto flex w-full max-w-6xl flex-1">
                {{ $slot }}
            </main>
            <footer class="border-t border-slate-200 bg-white dark:border-slate-900 dark:bg-slate-950/80">
                <div
                    class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-2 px-4 py-4 text-[11px] text-slate-500 dark:text-slate-500 sm:flex-row lg:px-6"
                >
                    <div class="flex items-center gap-2">
                        <span>Helix</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <a href="#" class="hover:text-slate-700 dark:hover:text-slate-300">Docs</a>
                        <a href="#" class="hover:text-slate-700 dark:hover:text-slate-300">GitHub</a>
                    </div>
                </div>
            </footer>
        </div>

        {!! Helix::js() !!}
    </body>
</html>
