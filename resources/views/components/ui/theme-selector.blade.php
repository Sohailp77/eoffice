<div x-data="{
    theme: localStorage.getItem('theme-color') || 'default',
    setTheme(val) {
        this.theme = val;
        localStorage.setItem('theme-color', val);
        if (val === 'default') {
            document.documentElement.removeAttribute('data-theme');
        } else {
            document.documentElement.setAttribute('data-theme', val);
        }
    }
}"
    class="relative flex items-center gap-2 bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm border border-gray-200 dark:border-gray-700 rounded-full p-1 shadow-sm">

    <!-- Tooltip/Label (Screen reader only usually, but visual here for clarity is nice) -->
    <span class="sr-only">Select Theme</span>

    <button @click="setTheme('default')"
        class="w-6 h-6 rounded-full border-2 transition-transform hover:scale-110 focus:outline-none"
        :class="theme === 'default' ? 'border-gray-900 dark:border-white scale-110' : 'border-transparent'"
        title="Emerald Professional">
        <span class="block w-full h-full rounded-full bg-[#155e46]"></span>
    </button>

    <button @click="setTheme('royal')"
        class="w-6 h-6 rounded-full border-2 transition-transform hover:scale-110 focus:outline-none"
        :class="theme === 'royal' ? 'border-gray-900 dark:border-white scale-110' : 'border-transparent'"
        title="Royal Gold">
        <span class="block w-full h-full rounded-full bg-[#c9a227]"></span>
    </button>

    <button @click="setTheme('ocean')"
        class="w-6 h-6 rounded-full border-2 transition-transform hover:scale-110 focus:outline-none"
        :class="theme === 'ocean' ? 'border-gray-900 dark:border-white scale-110' : 'border-transparent'"
        title="Ocean Blue">
        <span class="block w-full h-full rounded-full bg-[#1e40af]"></span>
    </button>

    <button @click="setTheme('berry')"
        class="w-6 h-6 rounded-full border-2 transition-transform hover:scale-110 focus:outline-none"
        :class="theme === 'berry' ? 'border-gray-900 dark:border-white scale-110' : 'border-transparent'"
        title="Berry Pink">
        <span class="block w-full h-full rounded-full bg-[#86198f]"></span>
    </button>

</div>