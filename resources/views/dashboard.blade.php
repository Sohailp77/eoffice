<x-layouts.app>
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white">eOffice Dashboard</h2>
            @if(auth()->user()->isAdmin())
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Manage users, modules, and system access.</p>
            @else
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Welcome to eOffice. Complete Your Tasks !</p>
            @endif
        </div>
        <div class="flex gap-3">
            @if(auth()->user()->isAdmin())
                <a href="{{ route('system.modules.create') }}"
                    class="px-5 py-2.5 bg-brand-dark text-white rounded-full text-sm font-semibold hover:bg-green-900 flex items-center gap-2">
                    <span>+</span> New Module
                </a>
            @endif
        </div>
    </div> 

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Modules -->
        <div
            class="bg-brand-dark rounded-3xl p-6 text-white shadow-lg relative overflow-hidden transition-transform hover:-translate-y-1 duration-300">
            <div class="flex justify-between items-start mb-4">
                <span class="text-sm font-medium opacity-90">System Modules</span>
                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                        </path>
                    </svg>
                </div>
            </div>
            <div class="text-4xl font-bold mb-6">{{ $totalModules }}</div>
            <div class="flex items-center gap-2 text-xs opacity-80">
                <span class="bg-green-500/30 px-1.5 py-0.5 rounded text-green-300 font-bold">Inst</span>
                <span>Installed Modules</span>
            </div>
        </div>

        <!-- Accessible Modules -->
        <div
            class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 transition-transform hover:-translate-y-1 duration-300">
            <div class="flex justify-between items-start mb-4">
                <span class="text-sm font-bold text-gray-800 dark:text-gray-200">Your Modules</span>
                <div
                    class="w-8 h-8 border border-gray-200 dark:border-gray-600 rounded-full flex items-center justify-center">
                    <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                        </path>
                    </svg>
                </div>
            </div>
            <div class="text-4xl font-bold mb-6 text-gray-900 dark:text-white">{{ $accessibleModules->count() }}</div>
            <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                <span
                    class="bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded text-gray-600 dark:text-gray-300 font-bold border dark:border-gray-600">Active</span>
                <span>Available to you</span>
            </div>
        </div>

        <!-- Total Users (Admin Only idea, but visible to all for dashboard look) -->
        <div
            class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 transition-transform hover:-translate-y-1 duration-300">
            <div class="flex justify-between items-start mb-4">
                <span class="text-sm font-bold text-gray-800 dark:text-gray-200">Total Users</span>
                <div
                    class="w-8 h-8 border border-gray-200 dark:border-gray-600 rounded-full flex items-center justify-center">
                    <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
                    </svg>
                </div>
            </div>
            <div class="text-4xl font-bold mb-6 text-gray-900 dark:text-white">{{ $totalUsers }}</div>
            <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                <span
                    class="bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded text-gray-600 dark:text-gray-300 font-bold border dark:border-gray-600">Reg</span>
                <span>Registered accounts</span>
            </div>
        </div>

        <!-- System Status -->
        <div
            class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 transition-transform hover:-translate-y-1 duration-300">
            <div class="flex justify-between items-start mb-4">
                <span class="text-sm font-bold text-gray-800 dark:text-gray-200">System Status</span>
                <div
                    class="w-8 h-8 border border-gray-200 dark:border-gray-600 rounded-full flex items-center justify-center">
                    <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="text-4xl font-bold mb-6 text-gray-900 dark:text-white">OK</div>
            <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                <span class="text-green-600 dark:text-green-400 font-medium">All systems operational</span>
            </div>
        </div>
    </div>

    <!-- Main Grid Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left Column (Users List) -->
        <div class="space-y-6">
            <!-- Team Collaboration -->
            <div
                class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-bold text-gray-900 dark:text-gray-100">Latest Users</h3>
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.users.index') }}"
                            class="text-xs border border-gray-200 dark:border-gray-600 px-3 py-1.5 rounded-full hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300">Manage</a>
                    @endif
                </div>
                <!-- Real Users -->
                <div class="space-y-5">
                    @foreach($recentUsers as $recentUser)
                        <div class="flex items-center gap-3 group">
                            <div
                                class="w-10 h-10 rounded-full border border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 flex items-center justify-center font-bold text-gray-500 dark:text-gray-300 group-hover:border-brand-primary group-hover:text-brand-primary transition-colors">
                                {{ substr($recentUser->first_name ?? $recentUser->username, 0, 1) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-gray-900 dark:text-gray-200">
                                    {{ $recentUser->full_name ?? $recentUser->username }}
                                </p>
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-400 truncate group-hover:text-brand-primary transition-colors">
                                    {{ $recentUser->email }}
                                </p>
                            </div>
                            <span
                                class="text-[10px] bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 px-2 py-1 rounded">Active</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Activity Mockup (Kept for UI balance) -->
            <!-- <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 h-64 flex flex-col">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-bold text-gray-900">System Traffic</h3>
                </div>
                <div class="flex-1 flex items-end justify-between gap-2 px-2">
                    @foreach(['S', 'M', 'T', 'W', 'T', 'F', 'S'] as $day)
                        <div class="flex flex-col items-center gap-2 w-full group cursor-pointer">
                            <div
                                class="w-full rounded-t-lg transition-all duration-300 
                                    {{ $day === 'W' ? 'bg-brand-dark h-28' : ($day === 'T' ? 'bg-brand-light h-20' : 'bg-gray-200 h-12 striped-bar opacity-50 hover:opacity-100') }}">
                            </div>
                            <span
                                class="text-xs text-gray-400 group-hover:text-gray-900 transition-colors">{{ $day }}</span>
                        </div>
                    @endforeach
                </div>
            </div> -->
        </div>

        <!-- Middle Column (Your Apps) - Moved here for better visibility as primary action area -->
        <div class="space-y-6 lg:col-span-2">
            <div
                class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-bold text-gray-900 dark:text-gray-100 text-xl">My Modules</h3>
                    <!-- <button class="text-xs border px-3 py-1.5 rounded-full hover:bg-gray-50">View All</button> -->
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($accessibleModules as $module)
                        <a href="{{ $module['route'] }}" wire:navigate
                            class="flex items-start gap-4 p-4 rounded-2xl border border-gray-100 dark:border-gray-700 hover:border-brand-primary/30 hover:bg-brand-accent/30 dark:hover:bg-brand-primary/20 transition-all group">
                            <div
                                class="w-12 h-12 rounded-xl bg-brand-accent/50 dark:bg-brand-primary/30 flex items-center justify-center text-brand-dark dark:text-brand-light flex-shrink-0 group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h4
                                    class="text-base font-bold text-gray-900 dark:text-gray-200 group-hover:text-brand-dark dark:group-hover:text-brand-light">
                                    {{ $module['name'] }}
                                </h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">
                                    {{ $module['description'] }}
                                </p>
                            </div>
                        </a>
                    @empty
                        <div class="col-span-full py-10 text-center text-gray-400">
                            No modules assigned. Contact administrator.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Quick Actions / Promo -->
            <div
                class="bg-gradient-to-r from-brand-dark to-brand-primary rounded-3xl p-8 text-white shadow-lg relative overflow-hidden flex items-center justify-between">
                <div class="relative z-10 max-w-md">
                    <h3 class="text-2xl font-bold mb-2">Need a new module?</h3>
                    <p class="text-brand-accent text-sm mb-6">You can request new modules or features from the system
                        administrator.</p>
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('system.modules.create') }}"
                            class="px-6 py-3 bg-white text-brand-dark rounded-xl font-bold text-sm hover:bg-gray-100 transition-colors">Create
                            Module</a>
                    @else
                        <button
                            class="px-6 py-3 bg-white/20 text-white rounded-xl font-bold text-sm hover:bg-white/30 transition-colors backdrop-blur-sm">Contact
                            Computer Section</button>
                    @endif
                </div>

                <!-- Decor -->
                <div class="absolute right-0 top-0 bottom-0 w-1/3 opacity-10">
                    <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                        <path d="M0 100 L100 0 L100 100 Z" fill="white" />
                    </svg>
                </div>
            </div>



        </div>
    </div>
</x-layouts.app>