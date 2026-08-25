<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport CHUSS</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #2e7d32;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #2e7d32;
            margin: 0 0 10px 0;
            font-size: 24px;
        }
        .header p {
            margin: 0;
            color: #555;
            font-size: 16px;
        }
        h2 {
            color: #1565c0;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
            margin-top: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .total-row {
            background-color: #e8f5e9;
            font-weight: bold;
        }
        .grand-total {
            margin-top: 40px;
            background-color: #2e7d32;
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 18px;
            border-radius: 5px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Centre Hospitalier Universitaire Sourô Sanou</h1>
        <p>Rapport de Consommation de Repas</p>
        <p><strong>Période :</strong> Du {{ $date_debut->format('d/m/Y') }} au {{ $date_fin->format('d/m/Y') }}</p>
    </div>

    <h2>1. Consommations du Personnel</h2>
    <table>
        <thead>
            <tr>
                <th>Type de Repas</th>
                <th>Quantité Consommée</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Petit-déjeuner</td>
                <td>{{ $statsPersonnel->get('petit_dejeuner', 0) }}</td>
            </tr>
            <tr>
                <td>Déjeuner</td>
                <td>{{ $statsPersonnel->get('dejeuner', 0) }}</td>
            </tr>
            <tr>
                <td>Dîner</td>
                <td>{{ $statsPersonnel->get('diner', 0) }}</td>
            </tr>
            <tr class="total-row">
                <td>Total Personnel</td>
                <td>{{ $statsPersonnel->sum() }}</td>
            </tr>
        </tbody>
    </table>

    <h2>2. Déclarations des Malades</h2>
    <table>
        <thead>
            <tr>
                <th>Régime</th>
                <th>Quantité Commandée</th>
            </tr>
        </thead>
        <tbody>
            @forelse($statsMalades['details'] as $regime => $quantite)
                <tr>
                    <td>{{ $regime }}</td>
                    <td>{{ $quantite }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" style="text-align: center; color: #777;">Aucune donnée pour les malades sur cette période.</td>
                </tr>
            @endforelse
            <tr class="total-row">
                <td>Total Malades</td>
                <td>{{ $statsMalades['total'] }}</td>
            </tr>
        </tbody>
    </table>

    <div class="grand-total">
        TOTAL GÉNÉRAL DES REPAS : {{ $totalGeneral }}
    </div>

    <div class="footer">
        Généré par SIGRH CHUSS le {{ now()->format('d/m/Y à H:i') }}
    </div>

</body>
</html>
