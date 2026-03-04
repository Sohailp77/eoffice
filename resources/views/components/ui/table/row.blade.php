<tr {{ $attributes->merge([
    'class' => 'border-b border-white/5 hover:bg-white/5 transition-colors duration-200 group'
]) }}>
    {{ $slot }}
</tr>