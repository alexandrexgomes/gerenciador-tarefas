<?php

namespace App\Application\DTOs;

class AutenticadoDTO
{
    public function __construct(
        public int $id,
        public readonly string $nome,
        public readonly string $email,
        public readonly int $status,
        public array $permissoes = []
    ) {}
}
