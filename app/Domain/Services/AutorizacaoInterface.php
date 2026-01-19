<?php

namespace App\Domain\Services;

use App\Domain\Enums\Permissao;

interface AutorizacaoInterface
{
    public function temPermissao(Permissao $permissao): bool;
    public function getUsuarioId(): int;
}
