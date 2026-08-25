<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Note d'Avertissement</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #000;
            margin: 0;
            padding: 40px;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            margin-bottom: 50px;
        }
        .logo {
            width: 120px;
            height: auto;
            margin-bottom: 10px;
        }
        .hospital-name {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }
        .title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            margin-top: 40px;
            margin-bottom: 30px;
        }
        .meta-info {
            margin-bottom: 30px;
        }
        .meta-info p {
            margin: 5px 0;
        }
        .message-box {
            margin-top: 30px;
            margin-bottom: 50px;
            text-align: justify;
        }
        .signature-box {
            margin-top: 80px;
            text-align: right;
            padding-right: 50px;
        }
        .signature-box .role {
            font-weight: bold;
            margin-bottom: 60px;
        }
        .signature-box .name {
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="header">
        <!-- Remplacer par le vrai chemin du logo si vous l'avez configuré publiquement ou en base64 -->
        @if(file_exists(public_path('images/logo.png')))
            <img src="{{ public_path('images/logo.png') }}" class="logo" alt="Logo CHUSS">
        @else
            <!-- Fallback textuel -->
            <div style="width:100px;height:100px;border:1px solid #ccc;border-radius:50%;display:inline-block;line-height:100px;margin-bottom:10px;">LOGO CHUSS</div>
        @endif
        <h1 class="hospital-name">Centre Hospitalier Universitaire Sourô Sanou</h1>
    </div>

    <div class="title">
        Note d'Avertissement
    </div>

    <div class="meta-info">
        <p><strong>Date :</strong> {{ $alerte->created_at->format('d/m/Y') }}</p>
        <p><strong>À l'attention de :</strong> Chef de service - {{ $alerte->service->nom }}</p>
        @if($alerte->beneficiaire)
            <p><strong>Concerne :</strong> Le bénéficiaire {{ $alerte->beneficiaire->nom }}</p>
        @endif
        <p><strong>Objet :</strong> {{ $alerte->titre }}</p>
    </div>

    <div class="message-box">
        {!! nl2br(e($alerte->message)) !!}
    </div>

    <div class="signature-box">
        <div class="role">Le Chef du Service Hôtellerie</div>
        <div class="name">Monsieur BATIONO</div>
    </div>

</body>
</html>
