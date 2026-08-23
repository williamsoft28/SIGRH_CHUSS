<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$data = [
    'lundi' => '2026-08-17',
    'categorie_jour' => [
        '2026-08-17' => 'garde',
        '2026-08-18' => 'aucune',
    ]
];

$lundi = \Carbon\Carbon::parse($data['lundi'])->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
$jours = collect(range(0, 6))->map(fn (int $i) => $lundi->copy()->addDays($i));

$categoriesJour = collect($data['categorie_jour'] ?? [])
    ->only($jours->map(fn (\Carbon\Carbon $j) => $j->toDateString())->all());

$categoriesRetenues = collect();
foreach ($jours as $jour) {
    $dateStr = $jour->toDateString();
    $categorie = $categoriesJour->get($dateStr, 'aucune');

    if ($categorie === 'apres_midi') {
        $categoriesRetenues->push('apres_midi');
    } elseif ($categorie === 'garde') {
        $categoriesRetenues->push('garde');
    }
}

var_dump($categoriesRetenues->isEmpty());
var_dump($categoriesRetenues->all());
var_dump($categoriesJour->all());
