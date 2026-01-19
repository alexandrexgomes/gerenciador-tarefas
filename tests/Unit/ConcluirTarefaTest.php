<?php

namespace Tests\Unit\UseCases\Tarefa;

use App\Application\UseCases\Tarefa\ConcluirTarefa;
use App\Domain\Entities\Tarefa;
use App\Domain\Enums\Permissao;
use App\Domain\Exceptions\NaoAutorizadoException;
use App\Domain\Exceptions\TarefaNaoEncontradaException;
use App\Domain\Repositories\TarefaRepositoryInterface;
use App\Domain\Services\AutorizacaoInterface;
use App\Domain\Services\TransactionManagerInterface;
use PHPUnit\Framework\TestCase;

final class ConcluirTarefaTest extends TestCase
{
    public function test_nao_deve_concluir_quando_nao_autorizado(): void
    {
        $repo = $this->createMock(TarefaRepositoryInterface::class);

        $auth = $this->createMock(AutorizacaoInterface::class);
        $auth->method('temPermissao')->with(Permissao::CONCLUIR_TAREFA)->willReturn(false);

        $tx = $this->createMock(TransactionManagerInterface::class);

        $useCase = new ConcluirTarefa($repo, $auth, $tx);

        $this->expectException(NaoAutorizadoException::class);
        $this->expectExceptionMessage('Você não tem permissão para concluir tarefas.');

        $useCase->executar(1);
    }

    public function test_deve_lancar_excecao_quando_tarefa_nao_existe(): void
    {
        $repo = $this->createMock(TarefaRepositoryInterface::class);
        $repo->method('buscarPorId')->with(999)->willReturn(null);

        $auth = $this->createMock(AutorizacaoInterface::class);
        $auth->method('temPermissao')->with(Permissao::CONCLUIR_TAREFA)->willReturn(true);

        $tx = $this->createMock(TransactionManagerInterface::class);
        $tx->method('executar')->willReturnCallback(fn(callable $cb) => $cb());

        $useCase = new ConcluirTarefa($repo, $auth, $tx);

        $this->expectException(TarefaNaoEncontradaException::class);
        $this->expectExceptionMessage('Tarefa não encontrada');

        $useCase->executar(999);
    }

    public function test_deve_concluir_e_persistir(): void
    {
        $tarefa = new Tarefa(
            id: 7,
            title: 'Comprar pilhas',
            description: null,
            completed: false,
            createdAt: '2026-01-18 09:00:00',
            updatedAt: '2026-01-18 09:00:00',
        );

        $repo = $this->createMock(TarefaRepositoryInterface::class);
        $repo->method('buscarPorId')->with(7)->willReturn($tarefa);

        $repo->expects($this->once())
            ->method('atualizar')
            ->with($this->callback(function (Tarefa $t) {
                $this->assertTrue($t->completed(), 'A tarefa deve estar concluída antes de persistir.');
                return true;
            }))
            ->willReturn(new Tarefa(
                id: 7,
                title: 'Comprar pilhas',
                description: null,
                completed: true,
                createdAt: '2026-01-18 09:00:00',
                updatedAt: '2026-01-18 09:05:00',
            ));

        $auth = $this->createMock(AutorizacaoInterface::class);
        $auth->method('temPermissao')->with(Permissao::CONCLUIR_TAREFA)->willReturn(true);

        $tx = $this->createMock(TransactionManagerInterface::class);
        $tx->method('executar')->willReturnCallback(fn(callable $cb) => $cb());

        $useCase = new ConcluirTarefa($repo, $auth, $tx);

        $dto = $useCase->executar(7);

        $this->assertSame(7, $dto->id);
        $this->assertTrue($dto->completed);
        $this->assertSame('2026-01-18 09:05:00', $dto->updated_at);
    }
}
