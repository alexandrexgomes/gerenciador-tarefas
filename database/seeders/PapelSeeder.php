<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PapelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('papeis')->insertOrIgnore([
            [
                'nome' => 'Administrador',
                'descricao' => 'Acesso total: gerencia usuarios, papeis e tarefas',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Usuario',
                'descricao' => 'Cria, edita e conclui tarefas conforme permissoes',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
