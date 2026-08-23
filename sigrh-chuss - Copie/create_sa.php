<?php
$user = \App\Models\User::firstOrCreate(
    ['email' => 'superadmin@chuss.cd'],
    ['name' => 'Super Administrateur', 'password' => 'password']
);
$user->assignRole('super_administrateur');
echo "Super admin created.\n";
