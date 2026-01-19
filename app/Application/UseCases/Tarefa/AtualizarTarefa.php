<?php

namespace App\Application\UseCases\Tarefa;

use App\Application\DTOs\Tarefa\AtualizarTarefaDTO;
use App\Application\DTOs\Tarefa\TarefaResponseDTO;
use App\Domain\Repositories\TarefaRepositoryInterface;
use App\Domain\Exceptions\TarefaNaoEncontradaException;

final class AtualizarTarefa
{
    public function __construct(
        private TarefaRepositoryInterface $tarefas
    ) {}

    public function executar(AtualizarTarefaDTO $dto): TarefaResponseDTO
    {
        $tarefa = $this->tarefas->buscarPorId($dto->id);

        if (!$tarefa) {
            throw new TarefaNaoEncontradaException($dto->id);
        }

        $tarefa->atualizar(
            title: $dto->title,
            description: $dto->description
        );

        if ($dto->completed !== null) {
            $dto->completed ? $tarefa->concluir() : $tarefa->reabrir();
        }

        $tarefaAtualizada = $this->tarefas->atualizar($tarefa);

        return new TarefaResponseDTO(
            id: $tarefaAtualizada->id(),
            title: $tarefaAtualizada->title(),
            description: $tarefaAtualizada->description(),
            completed: (bool) $tarefaAtualizada->completed(),
            created_at: $tarefaAtualizada->createdAt(),
            updated_at: $tarefaAtualizada->updatedAt(),
        );
    }
}
