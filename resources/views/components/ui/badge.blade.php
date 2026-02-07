@props(['color' => 'neutral', 'label'])

@php
    $colors = [
        'neutral' => 'bg-white/10 text-slate-300 border border-white/10',
        'green' => 'bg-green-500/20 text-green-600 border border-green-500/30',
        'success' => 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30',
        'warning' => 'bg-amber-500/20 text-amber-300 border border-amber-500/30',
        'danger' => 'bg-rose-500/20 text-rose-300 border border-rose-500/30',
        'purple' => 'bg-purple-500/20 text-purple-300 border border-purple-500/30',
    ];

    $classes = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium backdrop-blur-md shadow-sm ' . ($colors[$color] ?? $colors['neutral']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $label ?? $slot }}
</span>