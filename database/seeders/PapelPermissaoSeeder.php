<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PapelPermissaoSeeder extends Seeder
{
    public function run(): void
    {
        $papelId = fn (string $nome) => DB::table('papeis')->where('nome', $nome)->value('id');
        $permId  = fn (string $nome) => DB::table('permissoes')->where('nome', $nome)->value('id');

        $admin  = $papelId('Administrador');
        $user   = $papelId('Usuario');

        $adminPerms = [
            'tarefa.criar',
            'tarefa.listar_todas',
            'tarefa.ver_todas',
            'tarefa.atualizar',
            'tarefa.excluir',
            'tarefa.concluir',
            'tarefa.reabrir',

            'usuario.criar',
            'usuario.listar',
            'usuario.ver',
            'usuario.atualizar',
            'usuario.excluir',
        ];

        $userPerms = [
            'tarefa.criar',
            'tarefa.listar_proprias',
            'tarefa.ver_proprias',
            'tarefa.atualizar',
            'tarefa.concluir',
            'tarefa.reabrir',
        ];

        $rows = [];

        foreach ($adminPerms as $p) {
            $id = $permId($p);
            if ($admin && $id) {
                $rows[] = ['papel_id' => $admin, 'permissao_id' => $id];
            }
        }

        foreach ($userPerms as $p) {
            $id = $permId($p);
            if ($user && $id) {
                $rows[] = ['papel_id' => $user, 'permissao_id' => $id];
            }
        }

        DB::table('papel_permissao')->insertOrIgnore($rows);
    }
}