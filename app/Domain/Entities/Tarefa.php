<?php

namespace App\Domain\Entities;

final class Tarefa
{
    public const TAREFA_CONCLUIDA = 1;
    public const TAREFA_PENDENTE = 0;

    public function __construct(
        private ?int $id,
        private string $title,
        private ?string $description,
        private bool $completed,
        private ?string $createdAt = null,
        private ?string $updatedAt = null,
    ) {}

    public static function nova(string $title, ?string $description = null): self
    {
        return new self(
            id: null,
            title: $title,
            description: $description,
            completed: false
        );
    }

    public function id(): ?int
    {
        return $this->id;
    }
    public function title(): string
    {
        return $this->title;
    }
    public function description(): ?string
    {
        return $this->description;
    }
    public function completed(): bool
    {
        return $this->completed;
    }
    public function createdAt(): ?string
    {
        return $this->createdAt;
    }
    public function updatedAt(): ?string
    {
        return $this->updatedAt;
    }

    public function concluir(): void
    {
        $this->completed = true;
    }

    public function reabrir(): void
    {
        $this->completed = false;
    }

    public function atualizar(string $title, ?string $description): void
    {
        $this->title = $title;
        $this->description = $description;
    }
}
