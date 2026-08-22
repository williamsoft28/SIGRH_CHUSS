<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Plat;
use App\Models\Viande;
use App\Models\Sauce;
use Illuminate\Support\Facades\DB;

DB::statement('SET FOREIGN_KEY_CHECKS=0;');
Plat::truncate();
Viande::truncate();
Sauce::truncate();
DB::statement('SET FOREIGN_KEY_CHECKS=1;');

$platsPetitDej = ['BOUILLIE', 'CAFE AU LAIT'];
foreach ($platsPetitDej as $p) {
    Plat::create(['nom' => $p, 'type' => 'petit_dejeuner', 'actif' => true]);
}

$platsBase = [
    'RIZ GRAS SOUMBALA', 'HARICOT MIXTE', 'RIZ SAUCE ARACHIDE', 'MACARONI', 'RIZ SAUCE TOMATE', 'RIZ GRAS YASSA', 
    'SPAGUETTI SAUCE', 'RIZ SAUCE LEGUMES', 'TÔ SAUCE BAOBAB', 'RAGOÛT D\'IGNAME', 'REPAS SPECIAL', 'POIDS DE TERRE', 
    'COUSCOUS ARABE SAUCE', 'SAPAGUETTI GRAS', 'RIZ GRAS SAUCE', 'COUSCOUS ARABE GRAS', 'TÔ SAUCE OSEILLE', 
    'RIZ SAUCE DOUMGBLE', 'RIZ SAUCE GRAINE', 'SPAGUETTI SAUCE BOLONAISE', 'TÔ SAUCE GOMBO', 'RIZ SAUCE FEUILLES', 
    'ATTIEKE', 'HARICOT VERT', 'FARO / GNONKON'
];
foreach (array_unique($platsBase) as $p) {
    Plat::create(['nom' => $p, 'type' => 'plat_base', 'actif' => true]);
}

$desserts = ['ORANGE', 'BANANE', 'DÊGUÊ / YAOURT', 'MANGUE', 'PAPAYE', 'PASTEQUE'];
foreach ($desserts as $d) {
    Plat::create(['nom' => $d, 'type' => 'dessert', 'actif' => true]);
}

$viandes = ['VIANDE', 'POISSON'];
foreach ($viandes as $v) {
    Viande::create(['nom' => $v, 'actif' => true]);
}

echo "Database seeded with menu items successfully.\n";
