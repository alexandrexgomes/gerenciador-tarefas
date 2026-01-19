<?php

namespace App\Application\DTOs\Papel;

final class PapelListItemDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $nome,
    ) {}
}
