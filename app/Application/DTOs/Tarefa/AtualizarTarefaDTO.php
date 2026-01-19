<?php

namespace App\Application\DTOs\Tarefa;

final class AtualizarTarefaDTO
{
    public function __construct(
        public readonly int $id,
        public readonly ?string $title,
        public readonly ?string $description,
        public readonly ?bool $completed
    ) {}
}
