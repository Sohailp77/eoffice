<x-layouts.app>
    <x-slot:header>
        Reports
    </x-slot:header>

    <div class="mb-6">
        <h1 class="text-3xl font-bold text-white drop-shadow-lg">System Reports</h1>
        <p class="text-slate-300 mt-2 text-lg">Detailed insights and analytics for eOffice operations.</p>
    </div>

    <!-- Main Content Panel -->
    <div class="glass-panel p-8 rounded-2xl relative overflow-hidden">
        <!-- Decorative Gradient Blur -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-india-saffron/10 blur-3xl -z-10 rounded-full"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-india-green/10 blur-3xl -z-10 rounded-full"></div>

        <div class="flex flex-col items-center justify-center py-12 text-center">
            <div
                class="w-16 h-16 mb-6 rounded-full bg-white/5 flex items-center justify-center border border-white/10 shadow-[0_0_15px_rgba(255,153,51,0.1)] animate-pulse">
                <svg class="w-8 h-8 text-india-saffron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>

            <h3 class="text-xl font-semibold text-white mb-2">Reports Module Under Development</h3>
            <p class="text-slate-400 max-w-md mx-auto">
                We are currently building comprehensive reporting tools.
                <span class="text-india-saffron font-medium">Coming soon</span> with advanced metrics.
            </p>
        </div>
    </div>
</x-layouts.app>