<x-mail::message>
# Bonjour {{ $nom }},

Un compte SUS (déclarant) vient d'être créé pour vous sur SIGRH CHUSS, pour le service **{{ $service }}**.

Voici vos identifiants de connexion :

<x-mail::panel>
**Identifiant :** {{ $username }}<br>
**Mot de passe :** {{ $motDePasse }}
</x-mail::panel>

<x-mail::button :url="route('login')">
Se connecter
</x-mail::button>

Cette adresse email vous permettra de récupérer votre mot de passe si vous l'oubliez, via le lien « mot de passe oublié » de la page de connexion.

Pour votre sécurité, nous vous recommandons de conserver ces identifiants en lieu sûr.

Cordialement,<br>
{{ config('app.name') }}
</x-mail::message>
