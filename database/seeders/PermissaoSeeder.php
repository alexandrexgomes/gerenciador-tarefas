<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissoes = [
            ['nome' => 'tarefa.criar',          'descricao' => 'Criar tarefa'],
            ['nome' => 'tarefa.listar_proprias', 'descricao' => 'Listar apenas as próprias tarefas'],
            ['nome' => 'tarefa.ver_proprias',  'descricao' => 'Ver detalhes apenas das próprias tarefas'],
            ['nome' => 'tarefa.listar_todas',  'descricao' => 'Listar tarefas de todos os usuários'],
            ['nome' => 'tarefa.ver_todas',     'descricao' => 'Ver detalhes de tarefas de todos os usuários'],
            ['nome' => 'tarefa.atualizar',     'descricao' => 'Atualizar tarefa'],
            ['nome' => 'tarefa.excluir',       'descricao' => 'Excluir tarefa'],
            ['nome' => 'tarefa.concluir',      'descricao' => 'Concluir tarefa'],
            ['nome' => 'tarefa.reabrir',       'descricao' => 'Reabrir tarefa'],

            ['nome' => 'usuario.criar',      'descricao' => 'Criar usuário'],
            ['nome' => 'usuario.listar',     'descricao' => 'Listar usuários'],
            ['nome' => 'usuario.ver',        'descricao' => 'Ver usuário'],
            ['nome' => 'usuario.atualizar',  'descricao' => 'Atualizar usuário'],
            ['nome' => 'usuario.excluir',    'descricao' => 'Excluir usuário'],
        ];

        DB::table('permissoes')->insertOrIgnore(
            collect($permissoes)
                ->map(fn($p) => $p + ['created_at' => now(), 'updated_at' => now()])
                ->all()
        );
    }
}
