<?php

namespace Database\Seeders;

use App\Models\Beneficiaire;
use App\Models\Service;
use App\Models\Sus;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    /**
     * Seed a minimal demo dataset: 1 administrateur, 2 services, 1 utilisateur SUS
     * (avec sa fiche déclarant liée) et quelques bénéficiaires d'exemple.
     */
    public function run(): void
    {
        $administrateur = User::firstOrCreate(
            ['email' => 'admin@chuss.cd'],
            [
                'name' => 'Administrateur SIGRH',
                'password' => 'password',
            ]
        );
        $administrateur->assignRole('administrateur');

        $cardiologie = Service::firstOrCreate(
            ['code_service' => 'CARDIO'],
            [
                'nom' => 'Cardiologie',
                'type_service' => 'Médecine',
            ]
        );

        Service::firstOrCreate(
            ['code_service' => 'MEDINT'],
            [
                'nom' => 'Médecine interne',
                'type_service' => 'Médecine',
            ]
        );

        $sus = User::firstOrCreate(
            ['email' => 'sus.cardiologie@chuss.cd'],
            [
                'name' => 'SUS Cardiologie',
                'password' => 'password',
                'service_id' => $cardiologie->id,
            ]
        );
        $sus->assignRole('sus');

        Sus::updateOrCreate(
            ['login' => 'sus.cardiologie'],
            [
                'service_id' => $cardiologie->id,
                'nom' => 'SUS Cardiologie',
                'user_id' => $sus->id,
            ]
        );

        Beneficiaire::firstOrCreate(
            ['service_id' => $cardiologie->id, 'nom' => 'Jean Kalonji'],
            [
                'categorie' => 'Personnel continu',
                'type' => 'regulier',
                'numero_whatsapp' => '+243900000001',
            ]
        );

        Beneficiaire::firstOrCreate(
            ['service_id' => $cardiologie->id, 'nom' => 'Marie Tshibangu'],
            [
                'categorie' => 'Personnel de garde',
                'type' => 'variable',
                'numero_whatsapp' => '+243900000002',
            ]
        );

        $controleur = User::firstOrCreate(
            ['email' => 'controleur@chuss.cd'],
            [
                'name' => 'Contrôleur Réfectoire',
                'password' => 'password',
            ]
        );
        $controleur->assignRole('controleur');

        $hotellerie = User::firstOrCreate(
            ['email' => 'hotellerie@chuss.cd'],
            [
                'name' => 'Service Hôtellerie',
                'password' => 'password',
            ]
        );
        $hotellerie->assignRole('service_hotellerie');

        $prestataire = User::firstOrCreate(
            ['email' => 'prestataire@chuss.cd'],
            [
                'name' => 'Prestataire Restauration',
                'password' => 'password',
            ]
        );
        $prestataire->assignRole('prestataire');
    }
}
