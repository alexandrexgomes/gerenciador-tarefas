<?php

namespace App\Application\UseCases\Tarefa;

use App\Application\DTOs\Tarefa\TarefaResponseDTO;
use App\Domain\Enums\Permissao;
use App\Domain\Exceptions\NaoAutorizadoException;
use App\Domain\Exceptions\TarefaNaoEncontradaException;
use App\Domain\Repositories\TarefaRepositoryInterface;
use App\Domain\Services\AutorizacaoInterface;
use App\Domain\Services\TransactionManagerInterface;

final class ConcluirTarefa
{
    public function __construct(
        private TarefaRepositoryInterface $tarefa,
        private AutorizacaoInterface $autorizacao,
        private TransactionManagerInterface $transacaoManager,
    ) {}

    public function executar(int $id): TarefaResponseDTO
    {
        if (!$this->autorizacao->temPermissao(Permissao::CONCLUIR_TAREFA)) {
            throw new NaoAutorizadoException('Você não tem permissão para concluir tarefas.');
        }

        return $this->transacaoManager->executar(function () use ($id) {

            $tarefa = $this->tarefa->buscarPorId($id);

            if (!$tarefa) {
                throw new TarefaNaoEncontradaException($id);
            }

            $tarefa->concluir();
            $tarefa = $this->tarefa->atualizar($tarefa);
            return TarefaResponseDTO::fromEntity($tarefa);
        });
    }
}
