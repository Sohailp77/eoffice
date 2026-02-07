@props(['title', 'value', 'icon', 'color' => 'indigo'])

@php
    $gradients = [
        'indigo' => 'from-indigo-500 to-purple-500',
        'emerald' => 'from-emerald-500 to-teal-500',
        'amber' => 'from-amber-400 to-orange-500',
        'blue' => 'from-blue-400 to-cyan-500',
        'purple' => 'from-purple-500 to-pink-500',
        'red' => 'from-red-500 to-rose-500',
        'saffron' => 'from-india-saffron to-orange-500',
        'green' => 'from-india-green to-emerald-600',
        'navy' => 'from-india-blue to-indigo-900',
    ];

    $gradient = $gradients[$color] ?? $gradients['indigo'];
@endphp

<x-ui.card class="flex items-center gap-5 p-6 relative overflow-hidden group">
    <!-- Glow effect behind icon -->
    <div
        class="absolute -left-4 -top-4 w-24 h-24 bg-gradient-to-br {{ $gradient }} opacity-20 blur-2xl rounded-full group-hover:opacity-30 transition-opacity duration-500">
    </div>

    <div
        class="relative w-12 h-12 rounded-xl bg-gradient-to-br {{ $gradient }} flex items-center justify-center text-white shadow-lg shadow-indigo-500/20">
        {{ $icon }}
    </div>
    <div class="relative">
        <p class="text-sm font-medium text-slate-400 mb-1 tracking-wide uppercase">{{ $title }}</p>
        <p class="text-3xl font-bold text-white tracking-tight drop-shadow-sm">{{ $value }}</p>
    </div>
</x-ui.card>