<x-layouts.app>
    <x-ui.page-header title="User Management" description="Manage user access and roles." />

    <!-- Search & Filters -->
    <x-ui.card class="mb-6">
        <form action="{{ route('admin.users.index') }}" method="GET" class="flex gap-4">
            <div class="relative flex-1">
                <input type="text" name="search" 
                    value="{{ request('search') }}"
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
                <x-ui.button tag="a" href="{{ route('admin.users.index') }}" variant="secondary">Clear Search</x-ui.button>
            </div>
        @endif
    </x-ui.card>
    <x-ui.table>
        <x-slot:header>
            <x-ui.table.head>
                <x-ui.table.cell header>User Info</x-ui.table.cell>
                <x-ui.table.cell header>Email</x-ui.table.cell>
                <x-ui.table.cell header>Roles</x-ui.table.cell>
                <x-ui.table.cell header align="right">Actions</x-ui.table.cell>
            </x-ui.table.head>
        </x-slot:header>

        <tbody class="divide-y divide-white/10">
            @foreach($users as $user)
                <x-ui.table.row>
                    <x-ui.table.cell>
                        <div class="flex items-center">
                            <div
                                class="h-10 w-10 rounded-full dark:bg-brand-primary/20 bg-brand-accent flex items-center justify-center dark:text-brand-light text-brand-primary font-bold text-sm border border-brand-primary/30">
                                {{ substr($user->full_name, 0, 1) }}
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium dark:text-white text-black">{{ $user->full_name }}</div>
                                <div class="text-sm dark:text-gray-400 text-gray-600">{{ $user->email }}</div>
                            </div>
                        </div>
                    </x-ui.table.cell>
                    <x-ui.table.cell>
                        <div class="flex flex-wrap gap-2">
                            @foreach($user->systemRoles as $role)
                                <x-ui.badge :color="$role->slug === 'admin' ? 'green' : 'neutral'" :label="$role->name" />
                            @endforeach
                        </div>
                    </x-ui.table.cell>

                    <x-ui.table.cell align="right">
                        <form action="{{ route('admin.users.toggle-admin', $user->userid) }}" method="POST">
                            @csrf
                            @if($user->systemRoles->contains('id', $adminSystemRole->id))
                                <x-ui.button type="submit" variant="danger" size="sm">
                                    Revoke Admin
                                </x-ui.button>
                            @else
                                <x-ui.button type="submit" variant="primary" size="sm">
                                    Make Admin
                                </x-ui.button>
                            @endif
                        </form>
                    </x-ui.table.cell>
                </x-ui.table.row>
            @endforeach
        </tbody>

        <x-slot:footer>
            {{ $users->links() }}
        </x-slot:footer>
    </x-ui.table>

</x-layouts.app>