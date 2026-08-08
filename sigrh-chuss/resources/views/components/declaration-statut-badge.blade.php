@props(['statut'])

@php
    $libelles = [
        'en_saisie' => 'En saisie',
        'verrouillee' => 'Verrouillée',
        'validee' => 'Validée',
    ];

    $classes = [
        'en_saisie' => 'bg-blue-100 text-blue-800',
        'verrouillee' => 'bg-amber-100 text-amber-800',
        'validee' => 'bg-green-100 text-green-800',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex px-2 py-1 text-xs font-semibold rounded-full '.($classes[$statut] ?? 'bg-gray-100 text-gray-800')]) }}>
    {{ $libelles[$statut] ?? $statut }}
</span>
