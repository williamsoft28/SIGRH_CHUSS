<?php

use Illuminate\Http\Request;
use App\Http\Controllers\BeneficiaireController;
use Carbon\Carbon;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$request = Request::create('/test', 'POST', [
    'prenom' => 'Jean',
    'nom' => 'Dupont',
    'numero_whatsapp' => '0102030405',
    'lundi' => '2026-08-17',
    'categorie_jour' => [
        '2026-08-17' => 'garde',
        '2026-08-18' => 'aucune',
        '2026-08-19' => 'aucune',
        '2026-08-20' => 'aucune',
        '2026-08-21' => 'aucune',
        '2026-08-22' => 'aucune',
        '2026-08-23' => 'aucune',
    ]
]);

$data = $request->validate([
    'prenom' => ['required', 'string', 'max:255'],
    'nom' => ['required', 'string', 'max:255'],
    'numero_whatsapp' => ['required', 'string', 'max:20'],
    'lundi' => ['required', 'date'],
    'categorie_jour' => ['nullable', 'array'],
    'categorie_jour.*' => ['in:aucune,apres_midi,garde'],
]);

$lundi = Carbon::parse($data['lundi'])->copy()->startOfWeek(Carbon::MONDAY);
$jours = collect(range(0, 6))->map(fn (int $i) => $lundi->copy()->addDays($i));

$categoriesJour = collect($data['categorie_jour'] ?? [])
    ->only($jours->map(fn (Carbon $j) => $j->toDateString())->all());

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
