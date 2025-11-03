<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ProprietaireSeeder::class,
            BienSeeder::class,
            LocataireSeeder::class,
            GarantSeeder::class,
            ContratSeeder::class,
            DocumentTemplateSeeder::class,
        ]);
    }
}