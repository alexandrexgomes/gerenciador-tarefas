<?php

namespace App\Application\DTOs\Tarefa;

final class TarefaListItemDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $description,
        public readonly int $completed,
        public readonly string $created_at,
        public readonly string $updated_at,
    ) {}
}
