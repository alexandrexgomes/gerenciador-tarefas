<?php

namespace Tests\Unit\UseCases\Tarefa;

use App\Application\DTOs\Tarefa\CriarTarefaDTO;
use App\Application\UseCases\Tarefa\CriarTarefa;
use App\Domain\Entities\Tarefa;
use App\Domain\Enums\Permissao;
use App\Domain\Exceptions\NaoAutorizadoException;
use App\Domain\Repositories\TarefaRepositoryInterface;
use App\Domain\Services\AutorizacaoInterface;
use App\Domain\Services\TransactionManagerInterface;
use PHPUnit\Framework\TestCase;

final class CriarTarefaTest extends TestCase
{
    public function test_nao_deve_criar_quando_nao_autorizado(): void
    {
        $repo = $this->createMock(TarefaRepositoryInterface::class);

        $auth = $this->createMock(AutorizacaoInterface::class);
        $auth->method('temPermissao')->with(Permissao::CRIAR_TAREFA)->willReturn(false);

        $tx = $this->createMock(TransactionManagerInterface::class);

        $useCase = new CriarTarefa($repo, $auth, $tx);

        $this->expectException(NaoAutorizadoException::class);
        $this->expectExceptionMessage('Você não tem permissão para cadastrar tarefas.');

        $useCase->executar(new CriarTarefaDTO(
            title: 'Estudar Laravel',
            description: 'Revisar controllers e services',
        ));
    }

    public function test_deve_criar_quando_autorizado(): void
    {
        $repo = $this->createMock(TarefaRepositoryInterface::class);

        $auth = $this->createMock(AutorizacaoInterface::class);
        $auth->method('temPermissao')->with(Permissao::CRIAR_TAREFA)->willReturn(true);

        $tx = $this->createMock(TransactionManagerInterface::class);
        $tx->method('executar')->willReturnCallback(fn(callable $cb) => $cb());

        $repo->expects($this->once())
            ->method('criar')
            ->with($this->callback(function (Tarefa $t) {
                // A entidade "nova" deve vir sem ID e com completed false
                $this->assertNull($t->id());
                $this->assertSame('Estudar Laravel', $t->title());
                $this->assertSame('Revisar controllers e services', $t->description());
                $this->assertFalse($t->completed());
                return true;
            }))
            ->willReturn(new Tarefa(
                id: 10,
                title: 'Estudar Laravel',
                description: 'Revisar controllers e services',
                completed: false,
                createdAt: '2026-01-18 10:00:00',
                updatedAt: '2026-01-18 10:00:00',
            ));

        $useCase = new CriarTarefa($repo, $auth, $tx);

        $dto = $useCase->executar(new CriarTarefaDTO(
            title: 'Estudar Laravel',
            description: 'Revisar controllers e services',
        ));

        $this->assertSame(10, $dto->id);
        $this->assertSame('Estudar Laravel', $dto->title);
        $this->assertSame('Revisar controllers e services', $dto->description);
        $this->assertFalse($dto->completed);
        $this->assertSame('2026-01-18 10:00:00', $dto->created_at);
        $this->assertSame('2026-01-18 10:00:00', $dto->updated_at);
    }
}
