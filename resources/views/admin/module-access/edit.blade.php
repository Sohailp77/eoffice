<x-layouts.app>
    <div class="mb-6">
        <x-ui.button tag="a" href="{{ route('admin.module-access.index') }}" variant="ghost" size="sm">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Back to Users
        </x-ui.button>
    </div>

    <x-ui.page-header 
        title="Manage Module Access" 
        :description="'Configure module access for: ' . $user->username" 
    />

    @if(session('error'))
        <div class="mb-6 px-4 py-3 bg-red-500/10 border border-red-500/20 rounded-xl text-red-600 dark:text-red-400">
            {{ session('error') }}
        </div>
    @endif

    <!-- User Info Card -->
    <x-ui.card class="mb-8 bg-gradient-to-br from-brand-primary/5 to-transparent border-brand-primary/10">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-brand-primary/10 flex items-center justify-center text-brand-primary font-bold">
                    ID
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">User ID</p>
                    <p class="text-gray-900 dark:text-white font-mono font-medium">#{{ $user->userid }}</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-brand-primary/10 flex items-center justify-center text-brand-primary font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">Username</p>
                    <p class="text-gray-900 dark:text-white font-medium">{{ $user->username }}</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-brand-primary/10 flex items-center justify-center text-brand-primary font-bold">
                    @
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">Email</p>
                    <p class="text-gray-900 dark:text-white font-medium">{{ $user->email ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </x-ui.card>

    <!-- Module Access Form -->
    <form method="POST" action="{{ route('admin.module-access.update', $user->userid) }}">
        @csrf

        <x-ui.card>
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    Available Modules
                </h2>
                <span class="text-sm text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 px-3 py-1 rounded-full">
                    Select permissions below
                </span>
            </div>

            @if($modules->count() > 0)
                <div class="space-y-6">
                    @foreach($modules as $module)
                        {{-- Parent Module Item --}}
                        @php
                            $hasAccess = $userAccess->has($module->id);
                            $currentLevelId = $hasAccess ? $userAccess[$module->id]->module_level_id : null;
                        @endphp
                        
                        <div class="group">
                            <!-- Parent Card -->
                            <div class="p-5 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-xl transition-all hover:border-brand-primary/30 hover:shadow-sm">
                                <div class="flex items-start gap-4">
                                    <div class="pt-1">
                                        <input type="checkbox" name="modules[]" value="{{ $module->id }}" id="module_{{ $module->id }}"
                                            {{ $hasAccess ? 'checked' : '' }}
                                            class="w-5 h-5 text-brand-primary border-gray-300 dark:border-gray-600 rounded focus:ring-brand-primary/50 transition-colors cursor-pointer"
                                            onchange="toggleLevelDropdown({{ $module->id }})"
                                        />
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <label for="module_{{ $module->id }}" class="block text-gray-900 dark:text-white font-bold text-lg cursor-pointer select-none">
                                                {{ $module->name }}
                                            </label>
                                            @if($hasAccess)
                                                <span class="px-2.5 py-0.5 bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400 text-xs font-semibold rounded-full border border-green-200 dark:border-green-500/20">
                                                    Assigned
                                                </span>
                                            @endif
                                        </div>
                                        
                                        <div id="level_container_{{ $module->id }}" class="{{ $hasAccess ? '' : 'hidden' }} mt-3 animate-fade-in-down">
                                            <select name="levels[{{ $module->id }}]" id="level_{{ $module->id }}" 
                                                class="w-full sm:w-64 px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary/50 transition-shadow">
                                                <option value="">Select Access Level</option>
                                                @foreach($module->levels->sortBy('priority') as $level)
                                                    <option value="{{ $level->id }}" {{ $currentLevelId == $level->id ? 'selected' : '' }}>
                                                        {{ $level->name }} (Priority {{ $level->priority }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Sub Modules (Indented) -->
                            @if($module->subModules->count() > 0)
                                <div class="ml-6 pl-6 border-l-2 border-dashed border-gray-200 dark:border-gray-700 mt-3 space-y-3">
                                    @foreach($module->subModules as $sub)
                                        @php
                                            $subAccess = $userAccess->has($sub->id);
                                            $subLevelId = $subAccess ? $userAccess[$sub->id]->module_level_id : null;
                                        @endphp
                                        <div class="p-3 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-lg hover:border-gray-300 dark:hover:border-gray-600 transition-colors">
                                            <div class="flex items-start gap-3">
                                                <div class="pt-1">
                                                    <input type="checkbox" name="modules[]" value="{{ $sub->id }}" id="module_{{ $sub->id }}"
                                                        {{ $subAccess ? 'checked' : '' }}
                                                        class="w-4 h-4 text-brand-primary border-gray-300 dark:border-gray-600 rounded focus:ring-brand-primary/50 cursor-pointer"
                                                        onchange="toggleLevelDropdown({{ $sub->id }})"
                                                    />
                                                </div>
                                                <div class="flex-1">
                                                    <label for="module_{{ $sub->id }}" class="block text-gray-700 dark:text-gray-300 font-medium text-sm cursor-pointer select-none">
                                                        {{ $sub->name }}
                                                        <span class="text-xs text-gray-400 font-normal ml-1">(/{{ $sub->slug }})</span>
                                                    </label>
                                                    <div id="level_container_{{ $sub->id }}" class="{{ $subAccess ? '' : 'hidden' }} mt-2">
                                                        <select name="levels[{{ $sub->id }}]" id="level_{{ $sub->id }}" 
                                                            class="w-full sm:w-48 px-2 py-1.5 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded text-gray-700 dark:text-gray-300 text-xs focus:outline-none focus:ring-1 focus:ring-brand-primary/50">
                                                            <option value="">Select Level</option>
                                                            @forelse($sub->levels->sortBy('priority') as $level)
                                                                <option value="{{ $level->id }}" {{ $subLevelId == $level->id ? 'selected' : '' }}>
                                                                    {{ $level->name }}
                                                                </option>
                                                            @empty
                                                                <option value="" disabled>No levels defined</option>
                                                            @endforelse
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-4 mt-8 pt-6 border-t border-gray-100 dark:border-gray-700">
                    <x-ui.button type="submit" variant="primary" size="md">
                        Save Changes
                    </x-ui.button>
                    <x-ui.button tag="a" href="{{ route('admin.module-access.index') }}" variant="secondary" size="md">
                        Cancel
                    </x-ui.button>
                </div>
            @else
                <div class="text-center py-12">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <p class="text-gray-500 dark:text-gray-400 text-lg">No modules found in the system.</p>
                </div>
            @endif
        </x-ui.card>
    </form>

    <script>
        function toggleLevelDropdown(moduleId) {
            const checkbox = document.getElementById('module_' + moduleId);
            const container = document.getElementById('level_container_' + moduleId);
            
            if (checkbox.checked) {
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
                // Reset dropdown when unchecked to ensure cleaner submission (optional, but good UX)
                const selectInfo = document.getElementById('level_' + moduleId);
                if(selectInfo) selectInfo.value = '';
            }
        }
    </script>

    <style>
        .animate-fade-in-down {
            animation: fadeInDown 0.2s ease-out;
        }
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</x-layouts.app>
