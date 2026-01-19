<?php

namespace App\Application\DTOs\Usuario;

final class UsuarioListItemDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $nome,
        public readonly string $email,
        public readonly int $status,
        /** @var PapelListItemDTO[] */
        public readonly array $papeis = [],
    ) {}
}
