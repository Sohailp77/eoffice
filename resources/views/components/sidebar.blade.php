<aside
    class="w-64 bg-white dark:bg-gray-800 flex flex-col justify-between border-r border-gray-200 dark:border-gray-700 hidden md:flex flex-shrink-0 font-sans transition-colors duration-300">
    <div class="p-6">
        <div class="mb-8">
            <div class="flex items-center gap-3 text-brand-dark dark:text-brand-light">
                <div class="w-9 h-9 rounded-lg bg-brand-primary/10 flex items-center justify-center">
                    <span class="text-brand-primary font-bold text-sm">EO</span>
                </div>

                <div class="flex flex-col">
                    <h1 class="text-lg font-bold leading-tight">eOffice</h1>
                    <span class="text-xs text-gray-400">Workflow & documents</span>
                </div>
            </div>
        </div>



        <div class="mb-8">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4 pl-3">Menu</h3>
            <nav class="space-y-1">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" wire:navigate
                    class="flex items-center gap-3 px-3 py-3 rounded-xl font-semibold transition-all duration-200
                   {{ request()->routeIs('dashboard') ? 'bg-gray-50 dark:bg-gray-700 border-l-4 border-brand-dark dark:border-brand-light text-brand-dark dark:text-brand-light' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('dashboard') ? 'text-brand-dark dark:text-brand-light' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-900 dark:group-hover:text-gray-100' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                        </path>
                    </svg>
                    Dashboard
                </a>

                <!-- Modules Dynamic -->
                <!-- Modules Dynamic -->
                @if(isset($modules))
                    @foreach($modules as $module)
                        @continue($module->slug === 'dashboard-module')

                        @php
                            $slug = str_replace('-module', '', $module->slug);
                            $hasSubModules = $module->subModules->isNotEmpty();
                            $isActive = request()->is("{$slug}*"); 
                        @endphp

                        <div x-data="{ open: {{ $isActive ? 'true' : 'false' }} }" class="mb-1">
                            @if($hasSubModules)
                                <button @click="open = !open"
                                    class="w-full flex items-center justify-between px-3 py-3 rounded-xl font-medium transition-all duration-200 group text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100 {{ $isActive ? 'text-gray-900 dark:text-white' : '' }}">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-5 h-5 {{ $isActive ? 'text-brand-dark dark:text-brand-light' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-900 dark:group-hover:text-gray-100' }}"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                                            </path>
                                        </svg>
                                        {{ $module->name }}
                                    </div>
                                    <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': open}" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                                        </path>
                                    </svg>
                                </button>

                                <div x-show="open" x-collapse style="display: none;" class="mt-1 space-y-1 pl-11 pr-2">
                                    <a href="{{ url($slug) }}" wire:navigate
                                        class="block px-3 py-2 rounded-lg text-sm transition-colors {{ request()->fullUrlIs(url($slug)) ? 'bg-brand-primary/10 text-brand-primary font-medium' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                                        Dashboard
                                    </a>
                                    @foreach($module->subModules as $subModule)
                                        <a href="{{ url($slug . '/' . $subModule->slug) }}" wire:navigate
                                            class="block px-3 py-2 rounded-lg text-sm transition-colors {{ request()->is($slug . '/' . $subModule->slug . '*') ? 'bg-brand-primary/10 text-brand-primary font-medium' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                                            {{ $subModule->name }}
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <a href="{{ url($slug) }}" wire:navigate
                                    class="flex items-center gap-3 px-3 py-3 rounded-xl font-medium transition-all duration-200 group {{ $isActive ? 'bg-gray-50 dark:bg-gray-700 text-brand-dark dark:text-brand-light' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}">
                                    <svg class="w-5 h-5 {{ $isActive ? 'text-brand-dark dark:text-brand-light' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-900 dark:group-hover:text-gray-100' }}"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                                        </path>
                                    </svg>
                                    {{ $module->name }}
                                </a>
                            @endif
                        </div>
                    @endforeach
                @endif

                <!-- Admin Links (Collapsible or just listed) -->
                @if(auth()->user()->isAdmin())
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Admin Actions</p>

                        <a href="{{ route('admin.users.index') }}" wire:navigate
                            class="flex items-center gap-3 px-3 py-3 rounded-xl text-gray-500 hover:bg-gray-50 hover:text-gray-900">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                </path>
                            </svg>
                            Users
                        </a>
                        <a href="{{ route('system.modules.index') }}" wire:navigate
                            class="flex items-center gap-3 px-3 py-3 rounded-xl text-gray-500 hover:bg-gray-50 hover:text-gray-900">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                </path>
                            </svg>
                            Modules
                        </a>

                        <a href="{{ route('admin.module-access.index') }}" wire:navigate
                            class="flex items-center gap-3 px-3 py-3 rounded-xl text-gray-500 hover:bg-gray-50 hover:text-gray-900">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                </path>
                            </svg>
                            Assign Modules
                        </a>
                    </div>
                @endif
            </nav>
        </div>

    </div>

    <!-- Promo Card -->
    <div class="p-6">
        <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4 pl-3">
            <span class="text-brand-primary">{{ auth()->user()->username }}</span>
        </h3>
        <nav class="space-y-1 bg-brand-primary rounded-xl">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-3 px-3 py-3 rounded-xl text-white hover:bg-red-700 hover:text-white transition-all transition-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                        </path>
                    </svg>
                    Logout
                </button>
            </form>
        </nav>
    </div>
</aside>