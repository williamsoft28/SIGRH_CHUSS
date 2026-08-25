@component('mail::message')

# Nouveau menu soumis

Un nouveau menu a été soumis par le service hôtellerie pour la semaine commençant le **{{ $menu->date_debut->format('d/m/Y') }}**.

Vous pouvez le consulter et ajouter des observations en suivant le lien ci‑dessous.

@component('mail::button', ['url' => $lien])
Consulter le menu
@endcomponent

Merci,
L'équipe SIGRH CHUSS

@endcomponent
