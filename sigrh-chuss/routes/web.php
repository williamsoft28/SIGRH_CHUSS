<?php

use App\Http\Controllers\AdminBeneficiaireController;
use App\Http\Controllers\AdminBonRepasController;
use App\Http\Controllers\AdminDeclarationController;
use App\Http\Controllers\AdminDerogationController;
use App\Http\Controllers\AdminSusController;
use App\Http\Controllers\BeneficiaireController;
use App\Http\Controllers\BonPublicController;
use App\Http\Controllers\ControleurScanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SusBonRepasController;
use App\Http\Controllers\SusDeclarationController;
use App\Http\Controllers\SusDerogationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/bons/{codeUnique}', [BonPublicController::class, 'show'])->name('bons.public');
Route::get('/bons/{codeUnique}/telecharger', [BonPublicController::class, 'telecharger'])->name('bons.public.telecharger');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:sus'])->prefix('sus')->name('beneficiaires.')->group(function () {
    Route::get('/beneficiaires', [BeneficiaireController::class, 'index'])->name('index');
    Route::get('/beneficiaires/creer', [BeneficiaireController::class, 'create'])->name('create');
    Route::post('/beneficiaires', [BeneficiaireController::class, 'store'])->name('store');
    Route::get('/beneficiaires/{beneficiaire}/modifier', [BeneficiaireController::class, 'edit'])->name('edit');
    Route::put('/beneficiaires/{beneficiaire}', [BeneficiaireController::class, 'update'])->name('update');
});

Route::middleware(['auth', 'role:sus'])->prefix('sus/bons')->name('beneficiaires.bons.')->group(function () {
    Route::get('/{bon}', [SusBonRepasController::class, 'show'])->name('show');
    Route::get('/{bon}/telecharger', [SusBonRepasController::class, 'telecharger'])->name('telecharger');
    Route::post('/{bon}/envoyer-email', [SusBonRepasController::class, 'envoyerEmail'])->name('envoyer-email');
    Route::post('/{bon}/envoyer-whatsapp', [SusBonRepasController::class, 'envoyerWhatsapp'])->name('envoyer-whatsapp');
});

Route::middleware(['auth', 'role:sus'])->prefix('sus')->name('declarations.')->group(function () {
    Route::get('/declarations', [SusDeclarationController::class, 'index'])->name('index');
    Route::get('/declarations/creer', [SusDeclarationController::class, 'create'])->name('create');
    Route::post('/declarations', [SusDeclarationController::class, 'store'])->name('store');
});

Route::middleware(['auth', 'role:sus'])->prefix('sus')->name('derogations.')->group(function () {
    Route::get('/derogations', [SusDerogationController::class, 'index'])->name('index');
    Route::post('/derogations', [SusDerogationController::class, 'store'])->name('store');
});

Route::middleware(['auth', 'role:administrateur'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/declarations', [AdminDeclarationController::class, 'index'])->name('declarations.index');
    Route::post('/declarations/{declaration}/valider', [AdminDeclarationController::class, 'valider'])->name('declarations.valider');

    Route::get('/derogations', [AdminDerogationController::class, 'index'])->name('derogations.index');
    Route::post('/derogations', [AdminDerogationController::class, 'store'])->name('derogations.store');
    Route::post('/derogations/{derogation}/autoriser', [AdminDerogationController::class, 'autoriser'])->name('derogations.autoriser');
    Route::post('/derogations/{derogation}/refuser', [AdminDerogationController::class, 'refuser'])->name('derogations.refuser');

    Route::get('/bons/{bon}', [AdminBonRepasController::class, 'show'])->name('bons.show');
    Route::get('/bons/{bon}/telecharger', [AdminBonRepasController::class, 'telecharger'])->name('bons.telecharger');
    Route::post('/bons/{bon}/envoyer-email', [AdminBonRepasController::class, 'envoyerEmail'])->name('bons.envoyer-email');
    Route::post('/bons/{bon}/envoyer-whatsapp', [AdminBonRepasController::class, 'envoyerWhatsapp'])->name('bons.envoyer-whatsapp');

    Route::get('/beneficiaires/jour', [AdminBeneficiaireController::class, 'duJour'])->name('beneficiaires.jour');
    Route::get('/beneficiaires', [AdminBeneficiaireController::class, 'index'])->name('beneficiaires.index');
    Route::get('/beneficiaires/creer', [AdminBeneficiaireController::class, 'create'])->name('beneficiaires.create');
    Route::post('/beneficiaires', [AdminBeneficiaireController::class, 'store'])->name('beneficiaires.store');
    Route::get('/beneficiaires/{beneficiaire}/modifier', [AdminBeneficiaireController::class, 'edit'])->name('beneficiaires.edit');
    Route::put('/beneficiaires/{beneficiaire}', [AdminBeneficiaireController::class, 'update'])->name('beneficiaires.update');
    Route::delete('/beneficiaires/{beneficiaire}', [AdminBeneficiaireController::class, 'destroy'])->name('beneficiaires.destroy');

    Route::get('/sus', [AdminSusController::class, 'index'])->name('sus.index');
    Route::get('/sus/creer', [AdminSusController::class, 'create'])->name('sus.create');
    Route::post('/sus', [AdminSusController::class, 'store'])->name('sus.store');
    Route::post('/sus/{sus}/reinitialiser-mot-de-passe', [AdminSusController::class, 'reinitialiserMotDePasse'])->name('sus.reinitialiser-mot-de-passe');
});

Route::middleware(['auth', 'role:controleur'])->prefix('controleur')->name('controleur.')->group(function () {
    Route::get('/scanner', [ControleurScanController::class, 'index'])->name('scanner');
    Route::post('/verifier', [ControleurScanController::class, 'verifier'])->name('verifier');
});

require __DIR__.'/auth.php';
