<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Plages horaires des repas
    |--------------------------------------------------------------------------
    |
    | Utilisées par le contrôle au scan pour déterminer, à partir de l'heure
    | du scan, quel repas (petit_dejeuner, dejeuner, diner) est en cours.
    | Modifiable ici sans toucher au code métier ; penser à lancer
    | `php artisan config:clear` après modification si la config est mise en cache.
    |
    */

    'plages' => [
        'petit_dejeuner' => [
            'debut' => env('REPAS_PETIT_DEJEUNER_DEBUT', '06:00'),
            'fin' => env('REPAS_PETIT_DEJEUNER_FIN', '09:30'),
        ],
        'dejeuner' => [
            'debut' => env('REPAS_DEJEUNER_DEBUT', '11:30'),
            'fin' => env('REPAS_DEJEUNER_FIN', '14:30'),
        ],
        'diner' => [
            'debut' => env('REPAS_DINER_DEBUT', '18:00'),
            'fin' => env('REPAS_DINER_FIN', '21:00'),
        ],
    ],

];
