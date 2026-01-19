<?php

namespace App\Infrastructure\Adapters\Hash;

use Illuminate\Support\Facades\Hash;
use App\Domain\Services\Hash\ServiceHashInterface;

final class ServiceHash implements ServiceHashInterface
{
    public function gerarHash(string $senhaPura): string
    {
        return Hash::make($senhaPura);
    }

    public function verificar(string $senhaPura, string $hashGuardado): bool
    {
        return Hash::check($senhaPura, $hashGuardado);
    }
}
