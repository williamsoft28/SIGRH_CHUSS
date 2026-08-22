<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Menu Semaine {{ $menu->numero_semaine }} / {{ $menu->annee }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .text-center { text-align: center; }
        .logo { width: 100px; height: auto; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f3f4f6; }
        .bg-gray { background-color: #f9fafb; font-weight: bold; }
    </style>
</head>
<body>
    <div class="text-center">
        <img src="{{ public_path('images/logo.png') }}" class="logo" alt="Logo CHUSS">
        <h2>Menu de la Semaine {{ $menu->numero_semaine }} / {{ $menu->annee }}</h2>
        <p>Du {{ $menu->date_debut->format('d/m/Y') }} au {{ $menu->date_fin->format('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Jour</th>
                <th>Repas</th>
                <th>Plat</th>
                <th>Sauce</th>
                <th>Viande</th>
                <th>Dessert</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($menu->menuJours as $menuJour)
                @foreach (['petit_dejeuner' => 'Petit-déjeuner', 'dejeuner' => 'Déjeuner', 'diner' => 'Dîner'] as $type => $libelle)
                    @php
                        $repas = $menuJour->repas->firstWhere('type_repas', $type);
                    @endphp
                    <tr>
                        @if ($loop->first)
                            <td rowspan="3" class="bg-gray" style="vertical-align: middle;">
                                {{ $menuJour->jour_semaine }}<br>
                                {{ $menuJour->date_jour->format('d/m/Y') }}
                            </td>
                        @endif
                        <td>{{ $libelle }}</td>
                        <td>{{ $repas->plat->nom ?? '-' }}</td>
                        <td>{{ $repas->sauce->nom ?? '-' }}</td>
                        <td>{{ $repas->viande->nom ?? '-' }}</td>
                        <td>{{ $repas->dessert->nom ?? '-' }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>
</html>
