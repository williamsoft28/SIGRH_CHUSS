@component('mail::message')

# Menu validé et appliqué

Le menu de la semaine du **{{ $menu->date_debut->format('d/m/Y') }}** au **{{ $menu->date_fin->format('d/m/Y') }}** a été validé et est désormais actif.

Vous pouvez le télécharger via le lien ci-dessous.

@component('mail::button', ['url' => $lien])
Télécharger le menu
@endcomponent

Merci,
L'équipe SIGRH CHUSS

@endcomponent
