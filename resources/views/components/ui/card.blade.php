@props(['noPadding' => false])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm transition-all duration-300 hover:shadow-lg']) }}>
    <div class="{{ $noPadding ? '' : 'p-6' }}">
        {{ $slot }}
    </div>
</div>