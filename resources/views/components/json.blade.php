@props([
    "json" => [],
    "collapsed" => true,
])

<div
    class="mt-1 overflow-auto rounded bg-slate-100 text-xs text-slate-700 dark:bg-slate-900 dark:text-slate-100"
    x-data="{
        viewer: null,

        init() {
            this.viewer = new window.Jsnview(
                {{ \Illuminate\Support\Js::from($json) }},
                {
                    collapsed: {{ $collapsed ? "true" : "false" }},
                    showLen: false,
                    showType: false,
                    showFoldmarker: true,
                    maxDepth: Infinity,
                },
            )

            this.$el.appendChild(this.viewer.getElement())
        },

        collapseAll() {
            this.viewer?.collapseAll()
        },

        expandAll() {
            this.viewer?.expandAll()
        },
    }"
></div>
