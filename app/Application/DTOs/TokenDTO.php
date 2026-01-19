<?php

namespace App\Application\DTOs;

class TokenDTO
{
    public function __construct(
        public string $token,
        public int $expiraEmSegundos
    ) {}
}
