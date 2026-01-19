<?php

namespace App\Application\UseCases\Tarefa;

use App\Domain\Enums\Permissao;
use App\Domain\Exceptions\NaoAutorizadoException;
use App\Domain\Exceptions\TarefaNaoEncontradaException;
use App\Domain\Repositories\TarefaRepositoryInterface;
use App\Domain\Services\AutorizacaoInterface;
use App\Domain\Services\TransactionManagerInterface;

final class ExcluirTarefa
{
    public function __construct(
        private TarefaRepositoryInterface $tarefa,
        private AutorizacaoInterface $autorizacao,
        private TransactionManagerInterface $transacaoManager,
    ) {}

    public function executar(int $id): void
    {
        if (!$this->autorizacao->temPermissao(Permissao::EXCLUIR_TAREFA)) {
            throw new NaoAutorizadoException('Você não tem permissão para excluir tarefas.');
        }

        $this->transacaoManager->executar(function () use ($id) {

            $tarefa = $this->tarefa->buscarPorId($id);

            if (!$tarefa) {
                throw new TarefaNaoEncontradaException($id);
            }

            $this->tarefa->excluir($id);
            return null;
        });
    }
}
