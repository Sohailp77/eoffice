<x-layout>

    <div class="relative min-h-screen flex flex-col items-center justify-center overflow-hidden">

        <!-- Background Decorations -->
        <div class="absolute inset-0 w-full h-full pointer-events-none">
            <div
                class="absolute top-10 left-10 w-64 h-64 bg-brand-primary/10 rounded-full blur-3xl mix-blend-multiply animate-blob">
            </div>
            <div
                class="absolute bottom-10 right-10 w-80 h-80 bg-brand-accent/30 rounded-full blur-3xl mix-blend-multiply animate-blob animation-delay-2000">
            </div>
            <div
                class="absolute bottom-1/4 left-1/3 w-72 h-72 bg-brand-light/20 rounded-full blur-3xl mix-blend-multiply animate-blob animation-delay-4000">
            </div>
            <div class="absolute inset-0 noise-bg opacity-30"></div>
        </div>

        <!-- content -->
        <div class="relative z-10 w-full max-w-7xl px-6 lg:px-8 flex flex-col items-center text-center">

            <!-- Logo -->
            <div class="mb-6 animate-fade-in-up">
                <div class="inline-flex items-center gap-3 bg-white p-3 rounded-2xl shadow-[0_20px_40px_-15px_rgba(0,0,0,0.1)]">
                    <div
                        class="w-12 h-12 bg-brand-primary rounded-xl flex items-center justify-center text-white font-bold text-2xl shadow-lg shadow-brand-primary/30">
                        EO
                    </div>
                    <span class="text-2xl font-bold text-gray-900 tracking-tight pr-2">eOffice</span>
                </div>
            </div>

            <!-- Hero Text -->
            <h1 class="text-5xl md:text-7xl font-bold tracking-tight mb-8 animate-fade-in-up delay-100 text-gray-900">
                Simplify management<br>
                <span class="text-brand-primary relative inline-block mt-4">
                    with eOffice.
                    <svg class="absolute w-full h-3 -bottom-1 left-0 text-brand-accent -z-10" viewBox="0 0 100 10" preserveAspectRatio="none">
                        <path d="M0 5 Q 50 10 100 5" stroke="currentColor" stroke-width="8" fill="none" />
                    </svg>
                </span>
            </h1>

            <p class="max-w-2xl text-lg md:text-xl text-gray-500 mb-10 leading-relaxed animate-fade-in-up delay-200">
                Simplify your office management. Manage tasks, workflows, and documents in one unified platform.
            </p>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-4 animate-fade-in-up delay-300">
                <a href="{{ route('login') }}"
                    class="px-8 py-4 rounded-2xl bg-brand-primary text-white font-bold text-lg hover:bg-brand-dark transition-all duration-300 shadow-lg shadow-brand-primary/30 hover:shadow-brand-primary/40 transform hover:-translate-y-1 flex items-center gap-2">
                    <span>Access Workspace</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </a>
                
            </div>

            <!-- Footer / Status -->
            <div class="mt-20 pt-8 border-t border-gray-200 w-full max-w-md animate-fade-in-up delay-500">
                <div class="flex justify-between items-center text-sm text-gray-500">
                    <span>© {{ date('Y') }} eOffice</span>
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                        <span class="font-medium text-gray-700">System Online</span>
                    </div>
                </div>
            </div>

        </div>

    </div>

</x-layout>