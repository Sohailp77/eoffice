<x-layouts.app>
    <x-ui.page-header title="User Management" description="Manage user access and roles." />

    <!-- Search & Filters -->
    <x-ui.card class="mb-6">
        <form action="{{ route('admin.users.index') }}" method="GET" class="flex gap-4">
            <div class="relative flex-1">
                <input type="text" name="search" placeholder="Search by username, email, name..."
                    value="{{ request('search') }}"
                    class="w-full pl-10 pr-4 py-2 bg-white/5 border border-white/10 rounded-xl text-white ring-1 ring-brand-primary focus:ring-2 focus:ring-brand-primary/50 focus:border-brand-primary/50 outline-none transition-all placeholder:text-slate-500">
                <svg class="w-5 h-5 text-slate-500 absolute left-3 top-2.5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <x-ui.button type="submit" variant="primary">Search</x-ui.button>
        </form>
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