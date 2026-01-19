<?php

namespace App\Application\DTOs;

class AutenticarDTO
{
    public function __construct(
        public string $email,
        public string $password
    ) {}
}
