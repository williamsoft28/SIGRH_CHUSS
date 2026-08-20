<?php

namespace Database\Seeders;

use App\Models\Plat;
use App\Models\Sauce;
use App\Models\Viande;
use Illuminate\Database\Seeder;

class PlatsSaucesViandesSeeder extends Seeder
{
    /**
     * Liste de départ (cahier des charges CHUSS) pour les tables de référence
     * des menus. Modifiable ensuite par le service hôtellerie.
     */
    public function run(): void
    {
        $plats = [
            'petit_dejeuner' => [
                'Bouillie de petit mil', 'Bouillie de riz', 'Café', 'Lait', 'Pain',
            ],
            'plat_base' => [
                'Riz blanc', 'Riz gras', 'Riz à sauce', 'Riz local fumé', 'Spaghetti',
                'Macaroni', 'Couscous arabe', 'Tô maïs', 'Tô mil', 'Placali', 'Atiéké',
                'Fonio', 'Petit pois', 'Ragoût igname', 'Ragoût pomme de terre',
                'Ragoût patate douce', 'Haricot',
            ],
            'dessert' => [
                'Orange', 'Tangelot', 'Pamplemousse', 'Mangue', 'Banane douce',
                'Mandarine', 'Papaye', 'Pastèque', 'Melon', 'Yaourt',
                'Yaourt au couscous (dègué)', 'Gâteau', 'Jus naturel',
            ],
        ];

        foreach ($plats as $type => $noms) {
            foreach ($noms as $nom) {
                Plat::firstOrCreate(
                    ['nom' => $nom, 'type' => $type],
                    ['actif' => true]
                );
            }
        }

        $sauces = [
            'Sauce tomate', 'Sauce arachide', 'Sauce légumes', 'Sauce feuilles',
            'Sauce oseille', 'Sauce gombo', 'Sauce baobab', 'Gras',
        ];

        foreach ($sauces as $nom) {
            Sauce::firstOrCreate(['nom' => $nom], ['actif' => true]);
        }

        $viandes = [
            'Viande de bœuf', 'Viande de mouton', 'Volaille', 'Poisson', 'Œuf',
        ];

        foreach ($viandes as $nom) {
            Viande::firstOrCreate(['nom' => $nom], ['actif' => true]);
        }
    }
}
