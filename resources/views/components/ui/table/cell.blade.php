@props(['header' => false, 'align' => 'left'])

@php
    $baseClasses = $header
        ? 'px-6 py-4 text-xs font-bold text-indigo-200 uppercase tracking-wider'
        : 'px-6 py-4 whitespace-nowrap text-sm text-slate-300 group-hover:text-white transition-colors';

    $alignClasses = match ($align) {
        'left' => 'text-left',
        'center' => 'text-center',
        'right' => 'text-right',
        default => 'text-left',
    };

    $classes = "$baseClasses $alignClasses";
@endphp

@if($header)
    <th scope="col" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </th>
@else
    <td {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </td>
@endif