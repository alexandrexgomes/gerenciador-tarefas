<?php

namespace App\Application\DTOs\Usuario;

use App\Domain\Entities\Usuario;

final class UsuarioResponseDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $nome,
        public readonly string $email,
        public readonly int $status,
        public readonly array $papeis = []
    ) {}

    public static function fromEntity(Usuario $usuario, array $papeis = []): self
    {
        return new self(
            id: $usuario->id(),
            nome: $usuario->nome(),
            email: $usuario->email(),
            status: $usuario->status(),
            papeis: $papeis
        );
    }
}
