@props(['variant' => 'primary', 'size' => 'md', 'icon' => null, 'tag' => 'button'])

@php
    $baseClasses = 'inline-flex items-center justify-center font-bold tracking-wide transition-all duration-300 rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transform hover:-translate-y-0.5 active:translate-y-0';

    $variants = [
        'primary' => 'bg-green-600 text-white shadow-lg shadow-green-900/10 hover:bg-green-700 hover:shadow-green-900/20 border border-transparent',
        'secondary' => 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 shadow-sm',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 shadow-lg shadow-red-900/10 border border-transparent',
        'success' => 'bg-emerald-600 text-white hover:bg-emerald-700 shadow-lg shadow-emerald-900/10 border border-transparent',
        'ghost' => 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800',
    ];

    $sizes = [
        'sm' => 'px-4 py-1.5 text-xs',
        'md' => 'px-6 py-2.5 text-sm',
        'lg' => 'px-8 py-3.5 text-base',
    ];

    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

@if($tag === 'a')
    <a {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon) <span class="mr-2 -ml-1">{{ $icon }}</span> @endif
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon) <span class="mr-2">{{ $icon }}</span> @endif
        {{ $slot }}
    </button>
@endif