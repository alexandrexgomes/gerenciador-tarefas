<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PapelUsuarioSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $papelAdminId = DB::table('papeis')->where('nome', 'Administrador')->value('id');
        $papelUserId  = DB::table('papeis')->where('nome', 'Usuario')->value('id');

        $adminId   = DB::table('usuarios')->where('email', 'admin@tarefas.local')->value('id');
        $usuarioId = DB::table('usuarios')->where('email', 'usuario@tarefas.local')->value('id');
        $inativoId = DB::table('usuarios')->where('email', 'inativo@tarefas.local')->value('id');

        $rows = [];

        if ($adminId && $papelAdminId) {
            $rows[] = [
                'usuario_id' => $adminId,
                'papel_id'   => $papelAdminId,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if ($usuarioId && $papelUserId) {
            $rows[] = [
                'usuario_id' => $usuarioId,
                'papel_id'   => $papelUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if ($inativoId && $papelUserId) {
            $rows[] = [
                'usuario_id' => $inativoId,
                'papel_id'   => $papelUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('papel_usuario')->insertOrIgnore($rows);
    }
}
