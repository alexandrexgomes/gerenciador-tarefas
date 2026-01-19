<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Tarefa;

interface TarefaRepositoryInterface
{
    public function criar(Tarefa $tarefa): Tarefa;

    public function buscarPorId(int $id): ?Tarefa;

    public function atualizar(Tarefa $tarefa): Tarefa;

    public function excluir(int $id): bool;
}
