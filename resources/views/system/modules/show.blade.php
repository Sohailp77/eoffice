<x-layouts.app>
    <x-ui.page-header title="{{ $module->name }}" description="Module Details & Sub-modules" />

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Module Info -->
        <x-ui.card class="h-fit">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Module Information
            </h3>

            <div class="space-y-6">
                <div
                    class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-4 last:border-0 last:pb-0">
                    <span class="text-gray-500 dark:text-gray-400 font-medium">Name</span>
                    <span class="text-gray-900 dark:text-white font-semibold">{{ $module->name }}</span>
                </div>
                <div
                    class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-4 last:border-0 last:pb-0">
                    <span class="text-gray-500 dark:text-gray-400 font-medium">Slug</span>
                    <span
                        class="font-mono text-sm bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-gray-700 dark:text-gray-300">{{ $module->slug }}</span>
                </div>
                <div
                    class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-4 last:border-0 last:pb-0">
                    <span class="text-gray-500 dark:text-gray-400 font-medium">Status</span>
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-500/20 dark:text-green-400 border border-green-200 dark:border-green-500/20">
                        Active
                    </span>
                </div>
                <div
                    class="flex flex-col gap-2 border-b border-gray-100 dark:border-gray-700 pb-4 last:border-0 last:pb-0">
                    <span class="text-gray-500 dark:text-gray-400 font-medium">Root Directory</span>
                    <span
                        class="font-mono text-xs text-gray-600 dark:text-gray-400 break-all bg-gray-50 dark:bg-gray-900/50 p-2 rounded border border-gray-200 dark:border-gray-700">
                        app/Modules/{{ \Illuminate\Support\Str::studly($module->slug) }}
                    </span>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-700">
                <x-ui.button tag="a" href="{{ route('system.modules.index') }}" variant="secondary" size="sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                        </path>
                    </svg>
                    Back to Modules
                </x-ui.button>
            </div>
        </x-ui.card>

        <!-- Sub-Modules -->
        <x-ui.card>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                Sub-Modules (Resources)
            </h3>

            <!-- Create Sub-Module Form -->
            <form method="POST" action="{{ route('system.modules.submodules.store', $module->id) }}"
                class="mb-8 p-6 bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-gray-200 dark:border-gray-700">
                @csrf
                <p class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-4 uppercase tracking-wide">Add New
                    Sub-Module</p>
                <div class="space-y-4">
                    <div>
                        <input type="text" name="name" placeholder="Name (e.g. Sales Reports)" required
                            class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary/50 transition-all shadow-sm" />
                    </div>
                    <div class="flex gap-3">
                        <input type="text" name="slug" placeholder="Slug (e.g. reports)" required
                            class="flex-1 px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white text-sm font-mono focus:outline-none focus:ring-2 focus:ring-brand-primary/50 transition-all shadow-sm" />
                        <x-ui.button type="submit" variant="primary">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add
                        </x-ui.button>
                    </div>
                    <div
                        class="flex items-start gap-2 text-xs text-gray-500 dark:text-gray-400 bg-blue-50 dark:bg-blue-900/20 p-2 rounded text-blue-800 dark:text-blue-300">
                        <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p>Creates DB entry and appends <code>Route::resource('slug', ...)</code> to
                            <code>web.php</code>.
                        </p>
                    </div>
                </div>
            </form>

            <!-- Sub-Modules List -->
            <div class="space-y-3">
                @forelse($subModules as $sub)
                    <div
                        class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:border-brand-primary/30 transition-colors">
                        <div>
                            <span class="text-gray-900 dark:text-white font-semibold block">{{ $sub->name }}</span>
                            <span class="text-gray-500 dark:text-gray-400 font-mono text-xs flex items-center gap-1 mt-1">
                                <svg class="w-3 h-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                {{ $module->slug }}/{{ $sub->slug }}
                            </span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span
                                class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400">
                                Active
                            </span>
                            <form action="{{ route('system.modules.destroy', $sub->id) }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to delete this sub-module? ALL FILES WILL BE LOST.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all"
                                    title="Delete Sub-module">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <p class="text-gray-500 dark:text-gray-400 italic">No sub-modules defined yet.</p>
                    </div>
                @endforelse
            </div>
        </x-ui.card>
    </div>
</x-layouts.app>