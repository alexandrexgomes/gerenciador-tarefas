<?php

namespace App\Application\Services;

use App\Domain\Entities\Usuario;
use App\Application\Queries\UsuarioQueryRepositoryInterface;
use App\Application\DTOs\Usuario\UsuarioListItemDTO;
use App\Domain\Repositories\UsuarioRepositoryInterface;
use App\Application\DTOs\Paginacao\PaginacaoDTO;

final class UsuarioService
{
    public function __construct(
        private UsuarioQueryRepositoryInterface $query,
        private UsuarioRepositoryInterface $repo,
    ) {}

    /** @return UsuarioListItemDTO[] */
    public function listar(): array
    {
        return $this->query->listar();
    }

    public function paginate(array $filtros, int $page, int $perPage): PaginacaoDTO
    {
        return $this->query->paginate($filtros, $page, $perPage);
    }

    public function carregar(int $id): ?Usuario
    {
        return $this->repo->buscarPorId($id);
    }

    public function find(int $id): ?UsuarioListItemDTO
    {
        return $this->query->buscarPorId($id);
    }
}
