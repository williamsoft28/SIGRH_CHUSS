<x-mail::message>
# Bonjour {{ $nom }},

Votre bon de repas est prêt pour le service **{{ $service }}**.

<x-mail::panel>
**Repas concernés :** {{ $repas }}<br>
**Période :** {{ $dateDebut }}
@if ($dateDebut !== $dateFin)
 &rarr; {{ $dateFin }}
@endif
<br>
**Code :** {{ $codeUnique }}
</x-mail::panel>

Le QR code est joint à cet email. Vous pouvez aussi le consulter en ligne :

<x-mail::button :url="$lienPublic">
Voir mon bon de repas
</x-mail::button>

Présentez ce QR code au réfectoire pour chaque repas concerné.

Cordialement,<br>
{{ config('app.name') }}
</x-mail::message>
