<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            PapelSeeder::class,
            PermissaoSeeder::class,
            PapelPermissaoSeeder::class,
            UsuarioSeeder::class,
            PapelUsuarioSeeder::class,
            TarefasSeeder::class,
        ]);
    }
}