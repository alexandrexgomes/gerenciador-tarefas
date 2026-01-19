<?php

namespace App\Application\DTOs\Tarefa;

final class TarefaResponseDTO
{
    public function __construct(
        public int $id,
        public string $title,
        public ?string $description,
        public bool $completed,
        public string $created_at,
        public string $updated_at,
    ) {}
    public static function fromEntity(\App\Domain\Entities\Tarefa $tarefa): self
    {
        return new self(
            id: $tarefa->id(),
            title: $tarefa->title(),
            description: $tarefa->description(),
            completed: $tarefa->completed(),
            created_at: $tarefa->createdAt(),
            updated_at: $tarefa->updatedAt(),
        );
    }
}
