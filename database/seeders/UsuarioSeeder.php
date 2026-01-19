<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('usuarios')->insert([
            [
                'nome'       => 'Admin',
                'email'      => 'admin@tarefas.local',
                'password'   => Hash::make('admin123'),
                'status'     => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nome'       => 'Usuario Padrão',
                'email'      => 'usuario@tarefas.local',
                'password'   => Hash::make('usuario123'),
                'status'     => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nome'       => 'Inativo',
                'email'      => 'inativo@tarefas.local',
                'password'   => Hash::make('inativo123'),
                'status'     => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}