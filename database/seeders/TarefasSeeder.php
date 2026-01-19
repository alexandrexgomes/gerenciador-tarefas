<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TarefasSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $tarefas = [
            ['title' => 'Code review dos estagiários', 'description' => 'Revisar Pull Requests dos estagiários antes de aprovar o merge para a próxima release'],
            ['title' => 'Revisar backlog da sprint', 'description' => 'Validar prioridades e remover itens duplicados do backlog'],
            ['title' => 'Ajustar pipeline do CI', 'description' => 'Garantir que lint e testes rodem antes do merge na branch principal'],
            ['title' => 'Criar collection do Postman', 'description' => 'Montar collection com todos os endpoints e exemplos de payload'],
            ['title' => 'Documentar endpoints no README', 'description' => 'Descrever rotas, filtros, paginação e exemplos de resposta'],
            ['title' => 'Padronizar retornos de erro', 'description' => 'Garantir 422 para validação e 404 quando o recurso não existir'],
            ['title' => 'Adicionar filtros no GET /tasks', 'description' => 'Suportar filtros por completed, busca por texto e intervalo de datas'],
            ['title' => 'Criar endpoint de detalhar task', 'description' => 'Implementar GET /api/tasks/{id} com retorno no formato do teste'],
            ['title' => 'Criar endpoint de excluir task', 'description' => 'Implementar DELETE /api/tasks/{id} com retorno 204'],
            ['title' => 'Criar endpoint de concluir task', 'description' => 'Implementar PATCH /api/tasks/{id}/complete'],
            ['title' => 'Criar endpoint de reabrir task', 'description' => 'Implementar PATCH /api/tasks/{id}/reopen'],
            ['title' => 'Criar seed de papéis', 'description' => 'Popular papeis Administrador e Usuario'],
            ['title' => 'Criar seed de permissões', 'description' => 'Popular permissoes tarefa.* e usuario.*'],
            ['title' => 'Vincular permissões ao Administrador', 'description' => 'Garantir que o papel Administrador tenha todas as permissoes'],
            ['title' => 'Vincular permissões ao Usuario', 'description' => 'Garantir que Usuario tenha permissoes básicas de tarefas'],
            ['title' => 'Criar middleware ACL', 'description' => 'Proteger rotas de tasks com verificacao de permissao'],
            ['title' => 'Configurar auth para UsuarioModel', 'description' => 'Ajustar config/auth.php para usar a tabela usuarios'],
            ['title' => 'Criar login JWT', 'description' => 'Implementar endpoint de login e retorno do token'],
            ['title' => 'Adicionar refresh token', 'description' => 'Criar endpoint para renovar token JWT quando necessário'],
            ['title' => 'Garantir CORS para o front', 'description' => 'Configurar CORS para permitir chamadas do front-end'],
            ['title' => 'Adicionar paginação no listar tarefas', 'description' => 'Implementar per_page e page com limite máximo'],
            ['title' => 'Adicionar ordenação no listar tarefas', 'description' => 'Implementar sort e dir com whitelist de colunas'],
            ['title' => 'Criar testes de feature', 'description' => 'Testar criar/listar/atualizar tarefas com PHPUnit'],
            ['title' => 'Criar testes de autenticação', 'description' => 'Testar login e acesso com token JWT'],
            ['title' => 'Criar testes de ACL', 'description' => 'Validar 403 quando usuario não tem permissão'],
            ['title' => 'Revisar docker-compose', 'description' => 'Garantir que arquivos não sejam criados como root no volume'],
            ['title' => 'Padronizar timezone e datas', 'description' => 'Garantir created_at/updated_at no formato Y-m-d H:i:s'],
            ['title' => 'Criar script de setup', 'description' => 'Criar passo a passo para rodar projeto: build, migrate, seed'],
            ['title' => 'Revisar variáveis de ambiente', 'description' => 'Conferir APP_KEY, DB_HOST, DB_DATABASE, JWT_SECRET'],
            ['title' => 'Revisar qualidade do código', 'description' => 'Rodar phpstan/pint e corrigir warnings básicos'],
            ['title' => 'Preparar entrega do teste', 'description' => 'Validar endpoints no Postman e revisar README final'],
            ['title' => 'Refatorar controller de tasks', 'description' => 'Separar validação, filtros e responses para manter o controller limpo'],
            ['title' => 'Adicionar log de auditoria', 'description' => 'Registrar ações críticas (delete, complete) em log simples'],
        ];

        $rows = [];

        foreach ($tarefas as $i => $t) {
            
            $created = (clone $now)->subDays(rand(0, 40))->subMinutes(rand(0, 1200));
            $updated = (clone $created)->addMinutes(rand(0, 600));

            $rows[] = [
                'title'       => $t['title'],
                'description' => $t['description'],
                'completed'   => ($i % 5 === 0),
                'created_at'  => $created,
                'updated_at'  => $updated,
            ];
        }

        DB::table('tarefas')->insert($rows);
    }
}