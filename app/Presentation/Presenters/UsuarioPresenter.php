<?php

namespace App\Presentation\Presenters;

use App\Application\DTOs\Usuario\UsuarioResponseDTO;
use App\Application\DTOs\Usuario\UsuarioListItemDTO;
use App\Application\DTOs\Papel\PapelListItemDTO;
use App\Domain\Entities\Usuario;

final class UsuarioPresenter
{
    public static function detalhe(UsuarioResponseDTO $dto): array
    {
        return [
            'id'     => $dto->id,
            'nome'   => $dto->nome,
            'email'  => $dto->email,
            'status' => $dto->status,
            'status_desc' => self::statusDesc($dto->status),
            'papeis' => array_map(
                fn(PapelListItemDTO $p) => ['id' => $p->id, 'nome' => $p->nome],
                $dto->papeis ?? []
            ),
        ];
    }

    public static function lista(UsuarioListItemDTO $dto): array
    {
        return [
            'id'     => $dto->id,
            'nome'   => $dto->nome,
            'email'  => $dto->email,
            'status' => $dto->status,
            'status_desc' => self::statusDesc($dto->status),
            'papeis' => array_map(
                fn(PapelListItemDTO $p) => ['id' => $p->id, 'nome' => $p->nome],
                $dto->papeis ?? []
            ),
        ];
    }

    /** @param UsuarioListItemDTO[] $items */
    public static function colecao(array $items): array
    {
        return array_map(fn(UsuarioListItemDTO $i) => self::lista($i), $items);
    }

    private static function statusDesc(int $status): string
    {
        return $status === Usuario::STATUS_ATIVO ? 'Ativo' : 'Inativo';
    }
}
