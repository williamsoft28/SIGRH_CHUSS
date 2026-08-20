<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$col = collect(['2026-08-17' => 'garde', '2026-08-18' => 'aucune']);
$keys = ['2026-08-17', '2026-08-18', '2026-08-19'];

var_dump($col->only($keys)->all());
