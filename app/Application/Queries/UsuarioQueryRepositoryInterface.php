<?php

namespace App\Application\Queries;

use App\Application\DTOs\Papel\PapelListItemDTO;
use App\Application\DTOs\Usuario\UsuarioListItemDTO;
use App\Application\DTOs\Paginacao\PaginacaoDTO;

interface UsuarioQueryRepositoryInterface
{
    /** @return UsuarioListItemDTO[] */
    public function listar(): array;

    public function paginate(array $filtros, int $page, int $perPage): PaginacaoDTO;

    public function buscarPorId(int $id): ?UsuarioListItemDTO;

    /** @return PapelListItemDTO[] */
    public function carregarPapeisUsuario(int $usuarioId): array;
}
