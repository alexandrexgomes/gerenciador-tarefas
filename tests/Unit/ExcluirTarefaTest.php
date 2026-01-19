<?php

namespace Tests\Unit\UseCases\Tarefa;

use App\Application\UseCases\Tarefa\ExcluirTarefa;
use App\Domain\Entities\Tarefa;
use App\Domain\Enums\Permissao;
use App\Domain\Exceptions\NaoAutorizadoException;
use App\Domain\Exceptions\TarefaNaoEncontradaException;
use App\Domain\Repositories\TarefaRepositoryInterface;
use App\Domain\Services\AutorizacaoInterface;
use App\Domain\Services\TransactionManagerInterface;
use PHPUnit\Framework\TestCase;

final class ExcluirTarefaTest extends TestCase
{
    public function test_nao_deve_excluir_quando_nao_autorizado(): void
    {
        $repo = $this->createMock(TarefaRepositoryInterface::class);

        $auth = $this->createMock(AutorizacaoInterface::class);
        $auth->method('temPermissao')->with(Permissao::EXCLUIR_TAREFA)->willReturn(false);

        $tx = $this->createMock(TransactionManagerInterface::class);

        $useCase = new ExcluirTarefa($repo, $auth, $tx);

        $this->expectException(NaoAutorizadoException::class);
        $this->expectExceptionMessage('Você não tem permissão para excluir tarefas.');

        $useCase->executar(1);
    }

    public function test_deve_lancar_excecao_quando_tarefa_nao_existe(): void
    {
        $repo = $this->createMock(TarefaRepositoryInterface::class);
        $repo->method('buscarPorId')->with(404)->willReturn(null);

        $auth = $this->createMock(AutorizacaoInterface::class);
        $auth->method('temPermissao')->with(Permissao::EXCLUIR_TAREFA)->willReturn(true);

        $tx = $this->createMock(TransactionManagerInterface::class);
        $tx->method('executar')->willReturnCallback(fn(callable $cb) => $cb());

        $useCase = new ExcluirTarefa($repo, $auth, $tx);

        $this->expectException(TarefaNaoEncontradaException::class);
        $this->expectExceptionMessage('Tarefa não encontrada');

        $useCase->executar(404);
    }

    public function test_deve_excluir_quando_existe(): void
    {
        $repo = $this->createMock(TarefaRepositoryInterface::class);
        $repo->method('buscarPorId')->with(9)->willReturn(new Tarefa(
            id: 9,
            title: 'Apagar arquivos antigos',
            description: null,
            completed: false,
            createdAt: '2026-01-18 07:00:00',
            updatedAt: '2026-01-18 07:00:00',
        ));

        $repo->expects($this->once())
            ->method('excluir')
            ->with(9)
            ->willReturn(true);

        $auth = $this->createMock(AutorizacaoInterface::class);
        $auth->method('temPermissao')->with(Permissao::EXCLUIR_TAREFA)->willReturn(true);

        $tx = $this->createMock(TransactionManagerInterface::class);
        $tx->method('executar')->willReturnCallback(fn(callable $cb) => $cb());

        $useCase = new ExcluirTarefa($repo, $auth, $tx);

        $useCase->executar(9);

        $this->assertTrue(true); // se chegou aqui, passou
    }
}
