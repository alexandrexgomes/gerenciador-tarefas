<?php

namespace App\Domain\Services;

use App\Application\DTOs\AutenticadoDTO;
use App\Application\DTOs\TokenDTO;
use App\Domain\Entities\Usuario;

interface ProvedorTokenInterface
{
    public function gerarToken(Usuario $usuario): TokenDTO;
    public function usuarioAutenticado(): ?AutenticadoDTO;
}
