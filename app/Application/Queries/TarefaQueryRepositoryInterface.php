<?php

namespace App\Application\Queries;

use App\Application\DTOs\Paginacao\PaginacaoDTO;
use App\Application\DTOs\Tarefa\TarefaResponseDTO;
use App\Application\DTOs\Tarefa\TarefaListItemDTO;

interface TarefaQueryRepositoryInterface
{
    /** @return TarefaListItemDTO[] */
    public function listar(): array;

    /**
     * @param array<string, mixed> $filtros
     */
    public function paginate(array $filtros, int $page, int $perPage): PaginacaoDTO;

    public function carregar(int $id): ?TarefaResponseDTO;
}
