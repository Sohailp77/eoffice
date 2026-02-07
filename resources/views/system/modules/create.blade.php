<x-layouts.app>
    <x-ui.page-header title="Create Module" description="Scaffold a new system module" />

    <div class="glass-panel rounded-xl p-8 max-w-2xl dark:bg-gray-800/50 outline-none border-white/10">
        <form method="POST" action="{{ route('system.modules.store') }}">
            @csrf

            <div class="mb-6">
                <label class="block text-sm font-bold text-slate-400 uppercase mb-2">Module Name</label>
                <input type="text" name="name" placeholder="e.g. Inventory Management" required
                    class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 ring-1 ring-green-600 focus:ring-2 focus:ring-brand/50 focus:border-green-600/50 outline-none transition-all shadow-sm" />
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold text-slate-400 uppercase mb-2">Unique Slug</label>
                <input type="text" name="slug" placeholder="e.g. inventory" required
                    class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 ring-1 ring-green-600 focus:ring-2 focus:ring-brand/50 focus:border-green-600/50 outline-none transition-all shadow-sm" />
                <p class="text-xs text-slate-500 mt-2">This will be used for URLs and directory names.</p>
            </div>

            <div class="flex gap-4">
                <a href="{{ route('system.modules.index') }}"
                    class="px-4 py-2 dark:bg-white/5 bg-gray-200 dark:text-white tex-black rounded-lg hover:bg-gray-300 transition">
                    Cancel
                </a>
                <button type="submit"
                    class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">
                    Create Module
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>