<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Super Admin
        User::create([
            'name' => 'Admin GEST\'IMMO',
            'email' => 'admin@gestimmo.fr',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // 2. Gestionnaire
        User::create([
            'name' => 'Marie Dupont',
            'email' => 'marie.dupont@gestimmo.fr',
            'password' => bcrypt('password'),
            'role' => 'gestionnaire',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // 3. Propriétaire
        User::create([
            'name' => 'Jean Martin',
            'email' => 'jean.martin@example.com',
            'password' => bcrypt('password'),
            'role' => 'proprietaire',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // 4. Locataire
        User::create([
            'name' => 'Sophie Bernard',
            'email' => 'sophie.bernard@example.com',
            'password' => bcrypt('password'),
            'role' => 'locataire',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->command->info('✅ 4 utilisateurs créés avec succès !');
        $this->command->info('');
        $this->command->info('📧 Comptes de test :');
        $this->command->info('   Super Admin : admin@gestimmo.fr | password');
        $this->command->info('   Gestionnaire : marie.dupont@gestimmo.fr | password');
        $this->command->info('   Propriétaire : jean.martin@example.com | password');
        $this->command->info('   Locataire : sophie.bernard@example.com | password');
    }
}