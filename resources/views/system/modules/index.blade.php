<x-layouts.app>
    <x-ui.page-header title="Module Management" description="Create and manage system modules" />

    <div class="mb-6">
        <x-ui.button tag="a" href="{{ route('system.modules.create') }}" variant="primary">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Create New Module
        </x-ui.button>
    </div>

    <!-- Modules List -->
    <div class="space-y-4">
        @forelse($modules as $module)
            <!-- Only show parent modules here -->
            @if(is_null($module->parent_id))
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 hover:border-brand-primary/50 dark:hover:border-brand-primary/50 transition-colors shadow-sm">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white group">
                                <a href="{{ route('system.modules.show', $module->id) }}"
                                    class="group-hover:text-brand-primary transition-colors">
                                    {{ $module->name }}
                                </a>
                            </h3>
                            <p class="text-gray-500 dark:text-gray-400 font-mono text-sm mt-1">Slug: {{ $module->slug }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span
                                class="px-3 py-1 rounded-full text-xs font-medium bg-green-500/10 text-green-600 dark:bg-green-500/20 dark:text-green-400 border border-green-500/20">
                                Active
                            </span>
                            <form action="{{ route('system.modules.destroy', $module->id) }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to delete this WHOLE MODULE? ALL FILES WILL BE PERMANENTLY DELETED.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        @empty
            <div
                class="bg-white dark:bg-gray-800 rounded-xl p-12 text-center text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-700">
                No modules found.
            </div>
        @endforelse
    </div>
</x-layouts.app>