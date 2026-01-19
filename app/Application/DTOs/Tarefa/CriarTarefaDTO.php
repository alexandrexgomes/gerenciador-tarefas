<?php

namespace App\Application\DTOs\Tarefa;

final class CriarTarefaDTO
{
    public function __construct(
        public readonly string $title,
        public readonly string $description,
        public readonly bool $completed = false
    ) {}
}
