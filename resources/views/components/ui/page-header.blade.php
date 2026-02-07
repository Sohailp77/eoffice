@props(['title', 'description' => null])

<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight drop-shadow-sm">{{ $title }}</h1>
        @if($description)
            <p class="text-gray-600 dark:text-gray-400 mt-2 text-lg font-medium">{{ $description }}</p>
        @endif
    </div>
    @if(isset($actions))
        <div class="flex items-center gap-3">
            {{ $actions }}
        </div>
    @endif
</div>