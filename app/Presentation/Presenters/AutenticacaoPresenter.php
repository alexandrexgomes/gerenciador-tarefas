<?php

namespace App\Presentation\Presenters;

use App\Application\DTOs\AutenticadoDTO;
use App\Domain\Entities\Usuario;

final class AutenticacaoPresenter
{
    public static function perfil(AutenticadoDTO $dto): array
    {
        return [
            'id'     => $dto->id,
            'nome'   => $dto->nome,
            'email'  => $dto->email,
            'status' => $dto->status === Usuario::STATUS_ATIVO ? 'Ativo' : 'Inativo',
            'permissoes' => $dto->permissoes,
        ];
    }
}
