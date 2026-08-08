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
            'administrateur',
            'sus',
            'service_hotellerie',
            'controleur',
            'superviseur',
            'comite_suivi',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }
    }
}
