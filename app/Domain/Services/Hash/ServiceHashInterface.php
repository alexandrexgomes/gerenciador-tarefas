<?php

namespace App\Domain\Services\Hash;

interface ServiceHashInterface
{
    public function gerarHash(string $senhaPura): string;
    public function verificar(string $senhaPura, string $hashGuardado): bool;
}
