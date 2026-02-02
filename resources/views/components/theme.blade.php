<script>
setDarkClass = () => {
    if (localStorage.theme === 'dark' || ((!('theme' in localStorage) || !['light', 'dark'].includes(localStorage.theme)) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark')
    } else {
        document.documentElement.classList.remove('dark')
    }
}

setDarkClass()

window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', setDarkClass)
</script>

<div
    x-data="{
        dark: document.documentElement.classList.contains('dark'),
        toggle() {
            this.dark = !this.dark
            localStorage.theme = this.dark ? 'dark' : 'light'
            setDarkClass()
        },
    }"
>
    <button
        type="button"
        class="block rounded p-1 text-slate-600 hover:bg-slate-100 hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 focus:ring-offset-white dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-slate-50 dark:focus:ring-indigo-500 dark:focus:ring-offset-slate-950"
        @click="toggle()"
        :aria-pressed="dark.toString()"
    >
        <span class="sr-only" x-text="dark ? 'Switch to light mode' : 'Switch to dark mode'"></span>
        <svg class="w-5 h-5" :class="dark ? 'hidden' : 'block'" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sun-icon lucide-sun"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>
        <svg class="w-5 h-5" :class="dark ? 'block' : 'hidden'" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-moon-icon lucide-moon"><path d="M20.985 12.486a9 9 0 1 1-9.473-9.472c.405-.022.617.46.402.803a6 6 0 0 0 8.268 8.268c.344-.215.825-.004.803.401"/></svg>
    </button>
</div>
