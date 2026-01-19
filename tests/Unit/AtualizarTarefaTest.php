<?php

namespace Tests\Unit\UseCases\Tarefa;

use App\Application\DTOs\Tarefa\AtualizarTarefaDTO;
use App\Application\UseCases\Tarefa\AtualizarTarefa;
use App\Domain\Entities\Tarefa;
use App\Domain\Repositories\TarefaRepositoryInterface;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class AtualizarTarefaTest extends TestCase
{
    public function test_deve_lancar_excecao_quando_tarefa_nao_encontrada(): void
    {
        $repo = $this->createMock(TarefaRepositoryInterface::class);
        $repo->method('buscarPorId')->with(123)->willReturn(null);

        $useCase = new AtualizarTarefa($repo);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Tarefa não encontrada');

        $useCase->executar(new AtualizarTarefaDTO(
            id: 123,
            title: 'Novo título',
            description: 'Nova descrição',
            completed: false
        ));
    }

    public function test_deve_atualizar_titulo_descricao_e_manter_completed_false(): void
    {
        $tarefa = new Tarefa(
            id: 5,
            title: 'Antigo',
            description: 'Antiga desc',
            completed: false,
            createdAt: '2026-01-18 06:00:00',
            updatedAt: '2026-01-18 06:00:00',
        );

        $repo = $this->createMock(TarefaRepositoryInterface::class);
        $repo->method('buscarPorId')->with(5)->willReturn($tarefa);

        $repo->expects($this->once())
            ->method('atualizar')
            ->with($this->callback(function (Tarefa $t) {
                $this->assertSame('Novo', $t->title());
                $this->assertSame('Nova', $t->description());
                $this->assertFalse($t->completed());
                return true;
            }))
            ->willReturn(new Tarefa(
                id: 5,
                title: 'Novo',
                description: 'Nova',
                completed: false,
                createdAt: '2026-01-18 06:00:00',
                updatedAt: '2026-01-18 06:10:00',
            ));

        $useCase = new AtualizarTarefa($repo);

        $dto = $useCase->executar(new AtualizarTarefaDTO(
            id: 5,
            title: 'Novo',
            description: 'Nova',
            completed: false
        ));

        $this->assertSame(5, $dto->id);
        $this->assertSame('Novo', $dto->title);
        $this->assertSame('Nova', $dto->description);
        $this->assertFalse($dto->completed);
        $this->assertSame('2026-01-18 06:10:00', $dto->updated_at);
    }

    public function test_deve_atualizar_e_marcar_como_concluida_quando_completed_true(): void
    {
        $tarefa = new Tarefa(
            id: 6,
            title: 'Tarefa',
            description: null,
            completed: false,
            createdAt: '2026-01-18 05:00:00',
            updatedAt: '2026-01-18 05:00:00',
        );

        $repo = $this->createMock(TarefaRepositoryInterface::class);
        $repo->method('buscarPorId')->with(6)->willReturn($tarefa);

        $repo->expects($this->once())
            ->method('atualizar')
            ->with($this->callback(function (Tarefa $t) {
                $this->assertTrue($t->completed(), 'Se DTO completed=true, a entidade deve ficar concluída.');
                return true;
            }))
            ->willReturn(new Tarefa(
                id: 6,
                title: 'Tarefa',
                description: null,
                completed: true,
                createdAt: '2026-01-18 05:00:00',
                updatedAt: '2026-01-18 05:03:00',
            ));

        $useCase = new AtualizarTarefa($repo);

        $dto = $useCase->executar(new AtualizarTarefaDTO(
            id: 6,
            title: 'Tarefa',
            description: '',
            completed: true
        ));

        $this->assertSame(6, $dto->id);
        $this->assertTrue($dto->completed);
        $this->assertSame('2026-01-18 05:03:00', $dto->updated_at);
    }
}
