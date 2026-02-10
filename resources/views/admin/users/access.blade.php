<x-layout>
    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-white to-slate-400">
                    Manage Access: {{ $user->username }}
                </h1>
                <p class="text-slate-400 mt-1">Assign granular permissions and access levels.</p>
            </div>
            <a href="{{ route('admin.users.index') }}"
                class="px-4 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-slate-300 transition-all border border-white/10">
                Back to Users
            </a>
        </div>

        <form action="{{ route('admin.users.access.update', $user->id) }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($modules as $module)
                    <div class="glass-panel p-6 rounded-2xl relative overflow-hidden group">
                        <div
                            class="absolute inset-0 bg-gradient-to-br from-brand-primary/5 via-brand-light/5 to-brand-accent/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                        </div>

                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-xl font-semibold text-white">{{ $module->name }}</h3>
                                <span
                                    class="px-2 py-1 rounded-lg text-xs font-medium {{ $module->active ? 'bg-brand-primary/10 text-brand-primary border border-brand-primary/20' : 'bg-red-500/10 text-red-400 border border-red-500/20' }}">
                                    {{ $module->active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>

                            <div class="space-y-3">
                                @if($module->permissions->isEmpty())
                                    <p class="text-sm text-slate-500 italic">No specific permissions defined.</p>
                                @else
                                    @foreach($module->permissions as $permission)
                                        <label
                                            class="flex items-start gap-3 p-3 rounded-xl bg-white/5 hover:bg-white/10 border border-white/5 hover:border-white/20 transition-all cursor-pointer group/check">
                                            <div class="relative flex items-center mt-0.5">
                                                <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                                    class="peer sr-only" {{ in_array($permission->id, $userPermissions) ? 'checked' : '' }}>
                                                <div
                                                    class="w-5 h-5 rounded border-2 border-slate-500 peer-checked:border-brand-primary peer-checked:bg-brand-primary transition-all">
                                                </div>
                                                <svg class="absolute inset-0 w-5 h-5 text-white opacity-0 peer-checked:opacity-100 transition-opacity pointer-events-none"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </div>
                                            <div>
                                                <span
                                                    class="text-sm font-medium text-slate-300 peer-checked:text-white transition-colors">
                                                    {{ $permission->name }}
                                                </span>
                                                <p class="text-xs text-slate-500 mt-0.5">{{ $permission->slug }}</p>
                                            </div>
                                        </label>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 flex justify-end gap-4">
                <a href="{{ route('admin.users.index') }}"
                    class="px-6 py-3 rounded-xl text-slate-400 hover:text-white transition-all">
                    Cancel
                </a>
                <button type="submit"
                    class="px-6 py-3 rounded-xl bg-gradient-to-r from-brand-primary to-brand-dark text-white font-semibold shadow-lg shadow-brand-primary/20 hover:scale-[1.02] transition-all">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</x-layout>