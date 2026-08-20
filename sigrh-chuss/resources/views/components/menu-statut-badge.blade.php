@props(['statut'])

@php
    $libelles = [
        'soumis' => 'Soumis',
        'en_observation' => 'En observation',
        'valide' => 'Validé',
        'applique' => 'Appliqué',
        'rejete' => 'Rejeté',
    ];

    $classes = [
        'soumis' => 'bg-blue-100 text-blue-800',
        'en_observation' => 'bg-amber-100 text-amber-800',
        'valide' => 'bg-teal-100 text-teal-800',
        'applique' => 'bg-green-100 text-green-800',
        'rejete' => 'bg-red-100 text-red-800',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex px-2 py-1 text-xs font-semibold rounded-full '.($classes[$statut] ?? 'bg-gray-100 text-gray-800')]) }}>
    {{ $libelles[$statut] ?? $statut }}
</span>
