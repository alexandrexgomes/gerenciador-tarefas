<?php

namespace Tests\Unit\Domain\Entities;

use App\Domain\Entities\Tarefa;
use PHPUnit\Framework\TestCase;

final class TarefaEntityTest extends TestCase
{
    public function test_nova_deve_criar_sem_id_com_completed_false(): void
    {
        $tarefa = Tarefa::nova('Estudar PHPUnit', 'Criar unit tests');

        $this->assertNull($tarefa->id());
        $this->assertSame('Estudar PHPUnit', $tarefa->title());
        $this->assertSame('Criar unit tests', $tarefa->description());
        $this->assertFalse($tarefa->completed());
        $this->assertNull($tarefa->createdAt());
        $this->assertNull($tarefa->updatedAt());
    }

    public function test_nova_deve_aceitar_description_null(): void
    {
        $tarefa = Tarefa::nova('Sem descricao');

        $this->assertNull($tarefa->id());
        $this->assertSame('Sem descricao', $tarefa->title());
        $this->assertNull($tarefa->description());
        $this->assertFalse($tarefa->completed());
    }

    public function test_concluir_deve_marcar_como_concluida(): void
    {
        $tarefa = Tarefa::nova('Concluir algo');

        $this->assertFalse($tarefa->completed());

        $tarefa->concluir();

        $this->assertTrue($tarefa->completed());
    }

    public function test_reabrir_deve_marcar_como_pendente(): void
    {
        $tarefa = Tarefa::nova('Reabrir algo');
        $tarefa->concluir();

        $this->assertTrue($tarefa->completed());

        $tarefa->reabrir();

        $this->assertFalse($tarefa->completed());
    }

    public function test_concluir_duas_vezes_deve_ser_idempotente(): void
    {
        $tarefa = Tarefa::nova('Idempotencia concluir');

        $tarefa->concluir();
        $this->assertTrue($tarefa->completed());

        $tarefa->concluir();
        $this->assertTrue($tarefa->completed());
    }

    public function test_reabrir_duas_vezes_deve_ser_idempotente(): void
    {
        $tarefa = Tarefa::nova('Idempotencia reabrir');
        $tarefa->concluir();

        $tarefa->reabrir();
        $this->assertFalse($tarefa->completed());

        $tarefa->reabrir();
        $this->assertFalse($tarefa->completed());
    }

    public function test_atualizar_deve_alterar_title_e_description(): void
    {
        $tarefa = Tarefa::nova('Antes', 'Desc antes');

        $tarefa->atualizar('Depois', 'Desc depois');

        $this->assertSame('Depois', $tarefa->title());
        $this->assertSame('Desc depois', $tarefa->description());
    }

    public function test_atualizar_deve_permitir_description_null(): void
    {
        $tarefa = Tarefa::nova('Titulo', 'Tem desc');

        $tarefa->atualizar('Titulo novo', null);

        $this->assertSame('Titulo novo', $tarefa->title());
        $this->assertNull($tarefa->description());
    }

    public function test_constantes_de_estado(): void
    {
        $this->assertSame(1, Tarefa::TAREFA_CONCLUIDA);
        $this->assertSame(0, Tarefa::TAREFA_PENDENTE);
    }
}
