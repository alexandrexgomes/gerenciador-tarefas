<?php

namespace App\Application\Services;

use App\Domain\Entities\Tarefa;
use App\Application\Queries\TarefaQueryRepositoryInterface;
use App\Application\DTOs\Tarefa\TarefaListItemDTO;
use App\Domain\Repositories\TarefaRepositoryInterface;
use App\Application\DTOs\Paginacao\PaginacaoDTO;
use App\Application\DTOs\Tarefa\TarefaResponseDTO;

final class TarefaService
{
    public function __construct(
        private TarefaQueryRepositoryInterface $query
    ) {}

    /** @return TarefaListItemDTO[] */
    public function listar(): array
    {
        return $this->query->listar();
    }

    public function paginate(array $filtros, int $page, int $perPage): PaginacaoDTO
    {
        return $this->query->paginate($filtros, $page, $perPage);
    }

    public function carregar(int $id): ?TarefaResponseDTO
    {
        return $this->query->carregar($id);
    }
}
