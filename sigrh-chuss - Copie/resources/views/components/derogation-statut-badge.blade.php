@props(['statut'])

@php
    $libelles = [
        'demandee' => 'Demandée',
        'autorisee' => 'Autorisée',
        'refusee' => 'Refusée',
    ];

    $classes = [
        'demandee' => 'bg-amber-100 text-amber-800',
        'autorisee' => 'bg-green-100 text-green-800',
        'refusee' => 'bg-red-100 text-red-800',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex px-2 py-1 text-xs font-semibold rounded-full '.($classes[$statut] ?? 'bg-gray-100 text-gray-800')]) }}>
    {{ $libelles[$statut] ?? $statut }}
</span>
