@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex items-center px-4 py-3 mb-1 rounded-xl bg-gradient-to-r from-chuss-amber/20 to-chuss-amber/5 text-chuss-dark font-bold border-l-4 border-chuss-amber shadow-sm transition-all duration-300 transform scale-[1.02]'
            : 'flex items-center px-4 py-3 mb-1 rounded-xl text-chuss-gray font-medium hover:bg-white/60 hover:text-chuss-dark border-l-4 border-transparent hover:border-chuss-amber/50 transition-all duration-300 hover:translate-x-1 hover:shadow-sm backdrop-blur-sm';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
