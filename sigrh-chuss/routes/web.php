<?php

use App\Http\Controllers\AdminBeneficiaireController;
use App\Http\Controllers\AdminBonRepasController;
use App\Http\Controllers\AdminDeclarationController;
use App\Http\Controllers\AdminDerogationController;
use App\Http\Controllers\AdminSusController;
use App\Http\Controllers\BeneficiaireController;
use App\Http\Controllers\BonPublicController;
use App\Http\Controllers\ControleurScanController;
use App\Http\Controllers\PrestataireMenuController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceHotellerieMenuController;
use App\Http\Controllers\SusBonRepasController;
use App\Http\Controllers\SusDeclarationController;
use App\Http\Controllers\SusDerogationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/bons/{codeUnique}', [BonPublicController::class, 'show'])->name('bons.public');
Route::get('/bons/{codeUnique}/telecharger', [BonPublicController::class, 'telecharger'])->name('bons.public.telecharger');

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

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

Route::middleware(['auth', 'role:sus'])->prefix('sus/declarations-patients')->name('beneficiaires.declarations-patients.')->group(function () {
    Route::get('/', [\App\Http\Controllers\SusDeclarationPatientController::class, 'index'])->name('index');
    Route::get('/creer', [\App\Http\Controllers\SusDeclarationPatientController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\SusDeclarationPatientController::class, 'store'])->name('store');
});

Route::middleware(['auth', 'role:sus'])->prefix('sus')->name('derogations.')->group(function () {
    Route::get('/derogations', [SusDerogationController::class, 'index'])->name('index');
    Route::post('/derogations', [SusDerogationController::class, 'store'])->name('store');
});

Route::middleware(['auth', 'role:administrateur'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/declarations', [AdminDeclarationController::class, 'index'])->name('declarations.index');
    Route::post('/declarations/{declaration}/valider', [AdminDeclarationController::class, 'valider'])->name('declarations.valider');

    Route::get('/declarations-patients', [\App\Http\Controllers\AdminDeclarationPatientController::class, 'index'])->name('declarations_patients.index');

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
    
    // Zones
    Route::get('/zones', [\App\Http\Controllers\AdminZoneController::class, 'index'])->name('zones.index');
    Route::post('/zones', [\App\Http\Controllers\AdminZoneController::class, 'store'])->name('zones.store');
    Route::put('/zones/{zone}', [\App\Http\Controllers\AdminZoneController::class, 'update'])->name('zones.update');
    Route::delete('/zones/{zone}', [\App\Http\Controllers\AdminZoneController::class, 'destroy'])->name('zones.destroy');

    // Controle Service (Hotellerie)
    Route::get('/controle-service', [\App\Http\Controllers\AdminZoneServiceController::class, 'index'])->name('controle_service.index');
    Route::post('/controle-service/{zone}/valider', [\App\Http\Controllers\AdminZoneServiceController::class, 'valider'])->name('controle_service.valider');
    Route::post('/controle-service/{zone}/signaler', [\App\Http\Controllers\AdminZoneServiceController::class, 'signaler'])->name('controle_service.signaler');

    // Suivi Médical
    Route::get('/suivi-medical', [\App\Http\Controllers\AdminVisiteController::class, 'index'])->name('suivi_medical.index');
    Route::post('/suivi-medical', [\App\Http\Controllers\AdminVisiteController::class, 'store'])->name('suivi_medical.store');
    Route::put('/suivi-medical/{visite}', [\App\Http\Controllers\AdminVisiteController::class, 'update'])->name('suivi_medical.update');
});

Route::middleware(['auth', 'role:controleur'])->prefix('controleur')->name('controleur.')->group(function () {
    Route::get('/scanner', [ControleurScanController::class, 'index'])->name('scanner');
    Route::post('/verifier', [ControleurScanController::class, 'verifier'])->name('verifier');
});

Route::middleware(['auth', 'role:service_hotellerie'])->prefix('hotellerie')->name('hotellerie.')->group(function () {
    Route::get('/menus', [ServiceHotellerieMenuController::class, 'index'])->name('menus.index');
    Route::get('/menus/creer', [ServiceHotellerieMenuController::class, 'create'])->name('menus.create');
    Route::post('/menus', [ServiceHotellerieMenuController::class, 'store'])->name('menus.store');
    Route::get('/menus/{menu}', [ServiceHotellerieMenuController::class, 'show'])->name('menus.show');
    Route::put('/menus/{menu}', [ServiceHotellerieMenuController::class, 'update'])->name('menus.update');
    Route::post('/menus/{menu}/valider', [ServiceHotellerieMenuController::class, 'valider'])->name('menus.valider');
    Route::post('/observations/{observation}/traiter', [ServiceHotellerieMenuController::class, 'marquerObservationTraitee'])->name('observations.traiter');
});

Route::middleware(['auth', 'role:prestataire'])->prefix('prestataire')->name('prestataire.')->group(function () {
    Route::get('/menus', [PrestataireMenuController::class, 'index'])->name('menus.index');
    Route::get('/menus/historique', [PrestataireMenuController::class, 'historique'])->name('menus.historique');
    Route::get('/menus/{menu}', [PrestataireMenuController::class, 'show'])->name('menus.show');
    Route::put('/menus/{menu}', [PrestataireMenuController::class, 'update'])->name('menus.update');
    Route::post('/menus/{menu}/observations', [PrestataireMenuController::class, 'storeObservation'])->name('menus.observations.store');
    Route::post('/menus/{menu}/envoyer', [PrestataireMenuController::class, 'envoyerObservations'])->name('menus.envoyer');
});

Route::middleware(['auth', 'role:super_administrateur'])->prefix('super-admin')->name('super_admin.')->group(function () {
    Route::resource('users', \App\Http\Controllers\SuperAdminUserController::class);
    Route::resource('annees', \App\Http\Controllers\SuperAdminAnneeController::class);
    Route::post('annees/{annee}/archiver', [\App\Http\Controllers\SuperAdminAnneeController::class, 'archiver'])->name('annees.archiver');

    // Dérogations pour Super Admin
    Route::get('/derogations', [AdminDerogationController::class, 'index'])->name('derogations.index');
    Route::post('/derogations', [AdminDerogationController::class, 'store'])->name('derogations.store');
    Route::post('/derogations/tout-debloquer', [AdminDerogationController::class, 'toutDebloquer'])->name('derogations.tout-debloquer');
    Route::post('/derogations/tout-rebloquer', [AdminDerogationController::class, 'toutRebloquer'])->name('derogations.tout-rebloquer');
    Route::post('/derogations/{derogation}/autoriser', [AdminDerogationController::class, 'autoriser'])->name('derogations.autoriser');
    Route::post('/derogations/{derogation}/refuser', [AdminDerogationController::class, 'refuser'])->name('derogations.refuser');
    Route::delete('/derogations/{derogation}', [AdminDerogationController::class, 'destroy'])->name('derogations.destroy');
});

Route::middleware(['auth', 'role:service_hotellerie|prestataire'])->group(function () {
    Route::get('/menus/{menu}/telecharger', [\App\Http\Controllers\MenuDownloadController::class, 'download'])->name('menus.telecharger');
});

// Route temporaire pour configurer la base de données sur Render
Route::get('/setup-render', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
        \Illuminate\Support\Facades\Artisan::call('config:cache');
        \Illuminate\Support\Facades\Artisan::call('view:cache');
        return '<h1>Configuration réussie !</h1><p>Les tables et les données initiales ont été créées. <a href="/">Retour au site</a></p>';
    } catch (\Exception $e) {
        return '<h1>Erreur:</h1><p>' . $e->getMessage() . '</p>';
    }
});

Route::get('/force-admin', function () {
    try {
        $user = \App\Models\User::updateOrCreate(
            ['email' => 'admin@chuss.cd'],
            ['name' => 'Administrateur', 'password' => 'password']
        );
        // S'assurer que le rôle existe
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'administrateur', 'guard_name' => 'web']);
        $user->assignRole('administrateur');
        return '<h1>Compte Administrateur forcé !</h1><p>Email : <b>admin@chuss.cd</b><br>Mot de passe : <b>password</b></p><a href="/login">Aller se connecter</a>';
    } catch (\Exception $e) {
        return '<h1>Erreur:</h1><p>' . $e->getMessage() . '</p>';
    }
});

require __DIR__.'/auth.php';
