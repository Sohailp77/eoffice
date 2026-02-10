<x-layouts.app>
    <x-ui.page-header title="Module Access Management"
        description="Manage user access to modules and set access levels" />

    @if(session('success'))
        <div
            class="mb-6 px-4 py-3 bg-green-500/10 border border-green-500/20 rounded-xl text-green-600 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif

    <!-- Search -->
    <div class="mb-6">
        <form method="GET" action="{{ route('admin.module-access.index') }}" class="flex gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search by username, email, or name..."
                    class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none ring-1 ring-brand-primary focus:ring-2 focus:ring-brand/50 focus:border-brand-primary/50 outline-none transition-all shadow-sm" />
            </div>
            <x-ui.button type="submit" variant="primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                Search
            </x-ui.button>
            @if(request('search'))
                <x-ui.button tag="a" href="{{ route('admin.module-access.index') }}" variant="secondary">
                    Clear
                </x-ui.button>
            @endif
        </form>
    </div>

    <!-- Users Table -->
    <x-ui.table>
        <x-slot:header>
            <tr class="bg-gray-50 dark:bg-gray-900/50">
                <th
                    class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    User ID</th>
                <th
                    class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    User</th>
                <th
                    class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    Modules Assigned</th>
                <th
                    class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    Actions</th>
            </tr>
        </x-slot:header>

        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
            @forelse($users as $user)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                        #{{ $user->userid }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div
                                class="w-8 h-8 rounded-full bg-brand-primary/10 flex items-center justify-center text-brand-primary font-bold text-xs mr-3">
                                {{ substr($user->username, 0, 1) }}
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $user->full_name ?? $user->username }}
                                </div>
                                <div class="text-xs text-gray-500">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($user->moduleAccess->count() > 0)
                            <div class="flex flex-wrap gap-2">
                                @foreach($user->moduleAccess as $access)
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-brand-primary/10 text-brand-primary dark:bg-brand-primary/20 dark:text-brand-light border border-brand-primary/20 dark:border-brand-primary/20">
                                        {{ $access->module->name }}
                                        <span class="ml-1 opacity-60">({{ $access->level->name }})</span>
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <span class="text-gray-400 text-sm italic">No modules assigned</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <x-ui.button tag="a" href="{{ route('admin.module-access.edit', $user->userid) }}" variant="primary"
                            size="sm">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                </path>
                            </svg>
                            Manage
                        </x-ui.button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <p>No users found matching your search.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>

        <!-- Pagination -->
        @if($users->hasPages())
            <x-slot:footer>
                {{ $users->links() }}
            </x-slot:footer>
        @endif
    </x-ui.table>
</x-layouts.app>