@php
    if (! isset($scrollTo)) {
        $scrollTo = "body";
    }

    $scrollIntoViewJsSnippet =
        $scrollTo !== false
            ? <<<JS
               (\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()
            JS
            : "";
@endphp

<div>
    @if ($paginator->hasPages())
        <nav
            role="navigation"
            aria-label="Pagination Navigation"
            class="flex items-center justify-between"
        >
            {{-- Mobile --}}
            <div class="flex justify-between flex-1 sm:hidden">
                <span>
                    @if ($paginator->onFirstPage())
                        <span
                            class="inline-flex px-4 py-1 text-sm font-medium text-slate-400 bg-slate-100 border border-slate-200 rounded-md cursor-default dark:text-slate-500 dark:bg-slate-800 dark:border-slate-700"
                        >
                            {!! __("pagination.previous") !!}
                        </span>
                    @else
                        <button
                            type="button"
                            wire:click="previousPage('{{ $paginator->getPageName() }}')"
                            x-on:click="{{ $scrollIntoViewJsSnippet }}"
                            wire:loading.attr="disabled"
                            class="inline-flex px-4 py-1 text-sm font-medium text-slate-700 bg-white border border-slate-200 rounded-md hover:bg-slate-50 focus:outline-none focus:ring focus:ring-indigo-600 dark:text-slate-200 dark:bg-slate-800 dark:border-slate-700 dark:hover:bg-slate-700 dark:focus:ring-indigo-500"
                        >
                            {!! __("pagination.previous") !!}
                        </button>
                    @endif
                </span>

                <span>
                    @if ($paginator->hasMorePages())
                        <button
                            type="button"
                            wire:click="nextPage('{{ $paginator->getPageName() }}')"
                            x-on:click="{{ $scrollIntoViewJsSnippet }}"
                            wire:loading.attr="disabled"
                            class="inline-flex px-4 py-1 ml-3 text-sm font-medium text-slate-700 bg-white border border-slate-200 rounded-md hover:bg-slate-50 focus:outline-none focus:ring focus:ring-indigo-600 dark:text-slate-200 dark:bg-slate-800 dark:border-slate-700 dark:hover:bg-slate-700 dark:focus:ring-indigo-500"
                        >
                            {!! __("pagination.next") !!}
                        </button>
                    @else
                        <span
                            class="inline-flex px-4 py-1 ml-3 text-sm font-medium text-slate-400 bg-slate-100 border border-slate-200 rounded-md cursor-default dark:text-slate-500 dark:bg-slate-800 dark:border-slate-700"
                        >
                            {!! __("pagination.next") !!}
                        </span>
                    @endif
                </span>
            </div>

            {{-- Desktop --}}
            <div
                class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between"
            >
                <div>
                    <p class="text-sm text-slate-600 dark:text-slate-300">
                        <span>{{ __("Showing") }}</span>
                        <span class="font-medium">
                            {{ $paginator->firstItem() }}
                        </span>
                        <span>{{ __("to") }}</span>
                        <span class="font-medium">
                            {{ $paginator->lastItem() }}
                        </span>
                        <span>{{ __("of") }}</span>
                        <span class="font-medium">
                            {{ $paginator->total() }}
                        </span>
                        <span>{{ __("results") }}</span>
                    </p>
                </div>

                <div>
                    <span class="inline-flex rounded-md shadow-sm">
                        {{-- Previous --}}
                        @if ($paginator->onFirstPage())
                            <span
                                class="inline-flex px-2 py-1 text-slate-400 bg-slate-100 border border-slate-200 rounded-l-md cursor-default dark:text-slate-500 dark:bg-slate-800 dark:border-slate-700"
                            >
                                ‹
                            </span>
                        @else
                            <button
                                type="button"
                                wire:click="previousPage('{{ $paginator->getPageName() }}')"
                                x-on:click="{{ $scrollIntoViewJsSnippet }}"
                                class="inline-flex px-2 py-1 text-slate-700 bg-white border border-slate-200 rounded-l-md hover:bg-slate-50 focus:ring focus:ring-indigo-600 dark:text-slate-200 dark:bg-slate-800 dark:border-slate-700 dark:hover:bg-slate-700 dark:focus:ring-indigo-500"
                            >
                                ‹
                            </button>
                        @endif

                        {{-- Pages --}}
                        @foreach ($elements as $element)
                            @if (is_string($element))
                                <span
                                    class="px-4 py-1 text-slate-400 bg-slate-100 border border-slate-200 cursor-default dark:text-slate-500 dark:bg-slate-800 dark:border-slate-700"
                                >
                                    {{ $element }}
                                </span>
                            @endif

                            @if (is_array($element))
                                @foreach ($element as $page => $url)
                                    @if ($page == $paginator->currentPage())
                                        <span
                                            class="px-4 py-1 text-indigo-700 bg-indigo-50 border border-slate-200 cursor-default dark:text-indigo-200 dark:bg-indigo-500/10 dark:border-indigo-500/30"
                                        >
                                            {{ $page }}
                                        </span>
                                    @else
                                        <button
                                            type="button"
                                            wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                                            x-on:click="{{ $scrollIntoViewJsSnippet }}"
                                            class="px-4 py-1 text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 focus:ring focus:ring-indigo-600 dark:text-slate-200 dark:bg-slate-800 dark:border-slate-700 dark:hover:bg-slate-700 dark:focus:ring-indigo-500"
                                        >
                                            {{ $page }}
                                        </button>
                                    @endif
                                @endforeach
                            @endif
                        @endforeach

                        {{-- Next --}}

                        @if ($paginator->hasMorePages())
                            <button
                                type="button"
                                wire:click="nextPage('{{ $paginator->getPageName() }}')"
                                x-on:click="{{ $scrollIntoViewJsSnippet }}"
                                class="inline-flex px-2 py-1 text-slate-700 bg-white border border-slate-200 rounded-r-md hover:bg-slate-50 focus:ring focus:ring-indigo-600 dark:text-slate-200 dark:bg-slate-800 dark:border-slate-700 dark:hover:bg-slate-700 dark:focus:ring-indigo-500"
                            >
                                ›
                            </button>
                        @else
                            <span
                                class="inline-flex px-2 py-1 text-slate-400 bg-slate-100 border border-slate-200 rounded-r-md cursor-default dark:text-slate-500 dark:bg-slate-800 dark:border-slate-700"
                            >
                                ›
                            </span>
                        @endif
                    </span>
                </div>
            </div>
        </nav>
    @endif
</div>
