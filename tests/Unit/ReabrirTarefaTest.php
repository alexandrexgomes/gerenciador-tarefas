<?php

namespace Tests\Unit\UseCases\Tarefa;

use App\Application\UseCases\Tarefa\ReabrirTarefa;
use App\Domain\Entities\Tarefa;
use App\Domain\Enums\Permissao;
use App\Domain\Exceptions\NaoAutorizadoException;
use App\Domain\Exceptions\TarefaNaoEncontradaException;
use App\Domain\Repositories\TarefaRepositoryInterface;
use App\Domain\Services\AutorizacaoInterface;
use App\Domain\Services\TransactionManagerInterface;
use PHPUnit\Framework\TestCase;

final class ReabrirTarefaTest extends TestCase
{
    public function test_nao_deve_reabrir_quando_nao_autorizado(): void
    {
        $repo = $this->createMock(TarefaRepositoryInterface::class);

        $auth = $this->createMock(AutorizacaoInterface::class);
        $auth->method('temPermissao')->with(Permissao::REABRIR_TAREFA)->willReturn(false);

        $tx = $this->createMock(TransactionManagerInterface::class);

        $useCase = new ReabrirTarefa($repo, $auth, $tx);

        $this->expectException(NaoAutorizadoException::class);
        $this->expectExceptionMessage('Você não tem permissão para reabrir tarefas.');

        $useCase->executar(1);
    }

    public function test_deve_lancar_excecao_quando_tarefa_nao_existe(): void
    {
        $repo = $this->createMock(TarefaRepositoryInterface::class);
        $repo->method('buscarPorId')->with(55)->willReturn(null);

        $auth = $this->createMock(AutorizacaoInterface::class);
        $auth->method('temPermissao')->with(Permissao::REABRIR_TAREFA)->willReturn(true);

        $tx = $this->createMock(TransactionManagerInterface::class);
        $tx->method('executar')->willReturnCallback(fn(callable $cb) => $cb());

        $useCase = new ReabrirTarefa($repo, $auth, $tx);

        $this->expectException(TarefaNaoEncontradaException::class);
        $this->expectExceptionMessage('Tarefa não encontrada');

        $useCase->executar(55);
    }

    public function test_deve_reabrir_e_persistir(): void
    {
        $tarefa = new Tarefa(
            id: 3,
            title: 'Organizar pastas',
            description: 'Remover duplicados',
            completed: true,
            createdAt: '2026-01-18 08:00:00',
            updatedAt: '2026-01-18 08:30:00',
        );

        $repo = $this->createMock(TarefaRepositoryInterface::class);
        $repo->method('buscarPorId')->with(3)->willReturn($tarefa);

        $repo->expects($this->once())
            ->method('atualizar')
            ->with($this->callback(function (Tarefa $t) {
                $this->assertFalse($t->completed(), 'A tarefa deve estar reaberta antes de persistir.');
                return true;
            }))
            ->willReturn(new Tarefa(
                id: 3,
                title: 'Organizar pastas',
                description: 'Remover duplicados',
                completed: false,
                createdAt: '2026-01-18 08:00:00',
                updatedAt: '2026-01-18 08:45:00',
            ));

        $auth = $this->createMock(AutorizacaoInterface::class);
        $auth->method('temPermissao')->with(Permissao::REABRIR_TAREFA)->willReturn(true);

        $tx = $this->createMock(TransactionManagerInterface::class);
        $tx->method('executar')->willReturnCallback(fn(callable $cb) => $cb());

        $useCase = new ReabrirTarefa($repo, $auth, $tx);

        $dto = $useCase->executar(3);

        $this->assertSame(3, $dto->id);
        $this->assertFalse($dto->completed);
        $this->assertSame('2026-01-18 08:45:00', $dto->updated_at);
    }
}
