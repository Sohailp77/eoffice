<x-layout>
    <div
        class="flex h-screen overflow-hidden bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-100 font-sans transition-colors duration-300">
        <!-- Sidebar -->
        <x-sidebar />

        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col h-screen overflow-hidden">
            <!-- Header -->
            <header
                class="h-20 bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between px-8 flex-shrink-0 z-10 transition-colors duration-300">
                <div class="relative w-96 hidden md:block">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </span>
                    <input type="text" placeholder="Search task"
                        class="w-full pl-10 pr-12 py-2.5 bg-gray-50 text-gray-600 rounded-full text-sm focus:outline-none focus:ring-1 focus:ring-brand-primary border-none">
                </div>

                <div class="flex items-center gap-6 ml-auto">
                    <!-- Theme Selector -->
                    <x-ui.theme-selector />

                    <!-- Theme Toggle -->
                    <button id="theme-toggle"
                        class="w-10 h-10 rounded-full border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 bg-white transition-colors dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700">
                        <!-- Sun Icon -->
                        <svg id="theme-toggle-light-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                        <!-- Moon Icon -->
                        <svg id="theme-toggle-dark-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
                            </path>
                        </svg>
                    </button>

                    <!-- <div class="flex gap-2">
                        <button
                            class="w-10 h-10 rounded-full border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 bg-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                </path>
                            </svg>
                        </button>
                    </div> -->
                </div>

                <script>
                    function initializeThemeToggle() {
                        const toggleBtn = document.getElementById('theme-toggle');
                        const darkIcon = document.getElementById('theme-toggle-dark-icon');
                        const lightIcon = document.getElementById('theme-toggle-light-icon');

                        if (!toggleBtn) return;

                        function syncIcons() {
                            if (document.documentElement.classList.contains('dark')) {
                                darkIcon.classList.add('hidden');
                                lightIcon.classList.remove('hidden');
                            } else {
                                lightIcon.classList.add('hidden');
                                darkIcon.classList.remove('hidden');
                            }
                        }

                        // Ensure proper initial state from storage on any navigation
                        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                            document.documentElement.classList.add('dark');
                        } else {
                            document.documentElement.classList.remove('dark');
                        }

                        // Re-sync icons after potential class change
                        syncIcons();

                        // Remove old listener to avoid duplicates
                        const newBtn = toggleBtn.cloneNode(true);
                        toggleBtn.parentNode.replaceChild(newBtn, toggleBtn);

                        newBtn.addEventListener('click', () => {
                            const isDark = document.documentElement.classList.toggle('dark');
                            localStorage.theme = isDark ? 'dark' : 'light';
                            syncIcons();
                        });
                    }

                    // Run on initial load
                    document.addEventListener('DOMContentLoaded', initializeThemeToggle);

                    // Run on Livewire navigation (SPA-like transitions)
                    document.addEventListener('livewire:navigated', initializeThemeToggle);
                </script>

                <script>
                    // Immediate Theme Initialization to prevent flash
                    (function () {
                        const theme = localStorage.getItem('theme-color');
                        if (theme && theme !== 'default') {
                            document.documentElement.setAttribute('data-theme', theme);
                        }
                    })();
                </script>

            </header>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto p-8 no-scrollbar">
                {{ $slot }}
            </div>
            <x-ui.error />
            <x-ui.success />
        </main>
    </div>
</x-layout>