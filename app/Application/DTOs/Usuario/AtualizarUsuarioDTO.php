<?php

namespace App\Application\DTOs\Usuario;

final class AtualizarUsuarioDTO
{
    public function __construct(
        public readonly int $id,
        public readonly ?string $nome = null,
        public readonly ?string $email = null,
        public readonly ?string $password = null,
        public readonly ?int $status = null,
        /** @var int[] */
        public readonly array $papeis_ids = [],
    ) {}
}
