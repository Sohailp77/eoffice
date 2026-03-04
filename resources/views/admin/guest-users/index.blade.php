<x-layouts.app>
    <x-ui.page-header title="Guest Users" description="Manage guest accounts and assignments." />

    <!-- Search -->
    <x-ui.card class="mb-6">
        <form method="GET" action="{{ route('admin.guest-users.index') }}" class="flex gap-4">
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

            <x-ui.button tag="a" href="{{ route('admin.guest-users.create') }}" variant="primary">
                + New Guest User
            </x-ui.button>
        </form>
        @if(request('search'))
            <br>
            <div class="mt-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Showing results for "{{ request('search') }}"
                </p>
            </div>
        @endif

        <!-- clear search button -->
        @if(request('search'))
            <div class="mt-4">
                <x-ui.button tag="a" href="{{ route('admin.guest-users.index') }}" variant="secondary">Clear Search</x-ui.button>
            </div>
        @endif
    </x-ui.card>

    <x-ui.table>
        <x-slot:header>
            <x-ui.table.head>
                <x-ui.table.cell header>Name / Username</x-ui.table.cell>
                <x-ui.table.cell header>Email</x-ui.table.cell>
                <x-ui.table.cell header>Status</x-ui.table.cell>
                <x-ui.table.cell header>Module Access</x-ui.table.cell>
                <x-ui.table.cell header align="right">Actions</x-ui.table.cell>
            </x-ui.table.head>
        </x-slot:header>

        <tbody class="divide-y divide-white/10">
            @foreach($guests as $guest)
                <x-ui.table.row>
                    <x-ui.table.cell>
                        <div class="flex items-center">
                            <div class="h-10 w-10 rounded-full dark:bg-brand-primary/20 bg-brand-accent flex items-center justify-center dark:text-brand-light text-brand-primary font-bold text-sm border border-brand-primary/30">
                                {{ substr($guest->name, 0, 1) }}
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium dark:text-white text-black">{{ $guest->name }}</div>
                                <div class="text-xs text-gray-400">{{ $guest->username }}</div>
                            </div>
                        </div>
                    </x-ui.table.cell>
                    <x-ui.table.cell>
                        <span class="text-sm dark:text-gray-400 text-gray-600">{{ $guest->email }}</span>
                    </x-ui.table.cell>
                    <x-ui.table.cell>
                        @if($guest->is_active)
                            <x-ui.badge color="green" label="Active" size="sm" />
                        @else
                            <x-ui.badge color="danger" label="Inactive" size="sm" />
                        @endif
                    </x-ui.table.cell>

                    <x-ui.table.cell>
                        @if($guest->moduleAccess->isEmpty())
                            <span class="text-sm text-brand-primary dark:text-brand-light">No access</span>
                        @else
                            <div class="flex flex-wrap gap-1">
                                @foreach($guest->moduleAccess as $access)
                                    <x-ui.badge color="primary" label="{{ $access->module->name }} ({{ $access->level->name }})" size="sm" />
                                @endforeach
                            </div>
                        @endif
                    </x-ui.table.cell>

                    <x-ui.table.cell align="right">
                        <div class="flex justify-end gap-2">
                            <x-ui.button tag="a" href="{{ route('admin.module-access.edit', $guest->id) }}" variant="secondary" size="sm">
                                Manage Modules
                            </x-ui.button>
                            <x-ui.button tag="a" href="{{ route('admin.guest-users.edit', $guest->id) }}" variant="primary" size="sm">
                                Edit
                            </x-ui.button>
                            <form action="{{ route('admin.guest-users.destroy', $guest->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this guest user?');">
                                @csrf
                                @method('DELETE')
                                <x-ui.button type="submit" variant="danger" size="sm">Delete</x-ui.button>
                            </form>
                            <form action="{{ route('admin.guest-users.toggle-status', $guest->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to toggle the status of this guest user?');">
                                @csrf
                                @method('PATCH')
                                <x-ui.button type="submit" variant="{{ $guest->is_active ? 'warning' : 'success' }}" size="sm">{{ $guest->is_active ? 'Deactivate' : 'Activate' }}</x-ui.button>
                            </form>
                        </div>
                    </x-ui.table.cell>
                </x-ui.table.row>
            @endforeach
        </tbody>

        <x-slot:footer>
        </x-slot:footer>
    </x-ui.table>

</x-layouts.app>
