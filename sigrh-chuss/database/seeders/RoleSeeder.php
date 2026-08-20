<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Seed the application's roles.
     */
    public function run(): void
    {
        $roles = [
            'super_administrateur',
            'administrateur',
            'sus',
            'service_hotellerie',
            'prestataire',
            'controleur',
            'superviseur',
            'comite_suivi',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }
    }
}
