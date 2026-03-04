<x-layouts.app>
    <x-ui.page-header title="Create Guest User" description="Add a new guest user to the system." />

    <x-ui.card class="max-w-2xl">
        <form action="{{ route('admin.guest-users.store') }}" method="POST">
            @csrf

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="mt-1 form-input">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Username <span class="text-red-500">*</span></label>
                    <input type="text" name="username" value="{{ old('username') }}" required class="mt-1 form-input">
                    @error('username')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="mt-1 form-input">
                    @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Mobile</label>
                    <input type="text" name="mobile" value="{{ old('mobile') }}" class="mt-1 form-input">
                    @error('mobile')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password" required class="mt-1 form-input">
                    @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" required class="mt-1 form-input">
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" checked class="h-4 w-4 text-brand-primary border-gray-300 rounded focus:ring-brand-primary">
                    <label for="is_active" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">Active account</label>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-ui.button tag="a" href="{{ route('admin.guest-users.index') }}" variant="secondary">Cancel</x-ui.button>
                <x-ui.button type="submit" variant="primary">Create Guest User</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.app>
