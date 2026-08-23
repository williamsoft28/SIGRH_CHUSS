<?php

namespace App\Support;

use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * Génère l'identifiant et le mot de passe des comptes SUS créés par l'administrateur.
 */
class CompteSusGenerator
{
    /**
     * Identifiant au format sus.{service}@chuss, rendu unique si nécessaire.
     */
    public static function genererUsername(Service $service): string
    {
        $base = 'sus.'.Str::slug($service->nom, '').'@chuss';

        $username = $base;
        $suffixe = 2;

        while (User::where('username', $username)->exists()) {
            $username = 'sus.'.Str::slug($service->nom, '').$suffixe.'@chuss';
            $suffixe++;
        }

        return $username;
    }

    /**
     * Mot de passe fort de 6 caractères : au moins une majuscule, une minuscule,
     * un chiffre et un symbole, dans un ordre mélangé.
     */
    public static function genererMotDePasse(): string
    {
        $majuscules = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $minuscules = 'abcdefghijkmnpqrstuvwxyz';
        $chiffres = '23456789';
        $symboles = '!@#$%&*';

        $caracteres = [
            $majuscules[random_int(0, strlen($majuscules) - 1)],
            $minuscules[random_int(0, strlen($minuscules) - 1)],
            $chiffres[random_int(0, strlen($chiffres) - 1)],
            $symboles[random_int(0, strlen($symboles) - 1)],
        ];

        $tous = $majuscules.$minuscules.$chiffres.$symboles;

        while (count($caracteres) < 6) {
            $caracteres[] = $tous[random_int(0, strlen($tous) - 1)];
        }

        shuffle($caracteres);

        return implode('', $caracteres);
    }
}
