<?php

namespace App\Application\DTOs\Usuario;

use App\Domain\Entities\Usuario;

final class CadastrarUsuarioDTO
{
    public function __construct(
        public readonly int $id,
        public readonly ?string $nome = null,
        public readonly ?string $email = null,
        public readonly ?string $password = null,
        /** @var int[] */
        public readonly array $papeis_ids = [],
    ) {}
}
