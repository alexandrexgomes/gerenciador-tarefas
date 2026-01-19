<?php

namespace App\Application\UseCases\Tarefa;

use App\Application\DTOs\Tarefa\CriarTarefaDTO;
use App\Application\DTOs\Tarefa\TarefaResponseDTO;
use App\Domain\Entities\Tarefa;
use App\Domain\Enums\Permissao;
use App\Domain\Services\AutorizacaoInterface;
use App\Domain\Repositories\TarefaRepositoryInterface;
use App\Domain\Services\TransactionManagerInterface;
use App\Domain\Exceptions\NaoAutorizadoException;

final class CriarTarefa
{
    public function __construct(
        private TarefaRepositoryInterface $tarefa,
        private AutorizacaoInterface $autorizacao,
        private TransactionManagerInterface $transacaoManager,
    ) {}

    public function executar(CriarTarefaDTO $dto): TarefaResponseDTO
    {
        if (!$this->autorizacao->temPermissao(Permissao::CRIAR_TAREFA)) {
            throw new NaoAutorizadoException('Você não tem permissão para cadastrar tarefas.');
        }

        return $this->transacaoManager->executar(function () use ($dto) {

            $tarefa = Tarefa::nova(
                title: $dto->title,
                description: $dto->description
            );

            $tarefa = $this->tarefa->criar($tarefa);
            return TarefaResponseDTO::fromEntity($tarefa);
        });
    }
}
