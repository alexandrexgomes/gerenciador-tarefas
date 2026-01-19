<?php

namespace App\Infrastructure\Persistence\Eloquent\QueryRepositories;

use App\Application\DTOs\Paginacao\PaginacaoDTO;
use App\Application\DTOs\Tarefa\TarefaResponseDTO;
use App\Application\Queries\TarefaQueryRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Models\TarefaModel;
use App\Application\DTOs\Tarefa\TarefaListItemDTO;

final class TarefaQueryRepository implements TarefaQueryRepositoryInterface
{
    public function paginate(array $filtros, int $page, int $perPage): PaginacaoDTO
    {
        $q = TarefaModel::query();

        if (!empty($filtros['created_from'])) {
            $q->where('created_at', '>=', $filtros['created_from']);
        }

        if (!empty($filtros['created_to'])) {
            $q->where('created_at', '<=', $filtros['created_to']);
        }

        if (isset($filtros['completed']) && $filtros['completed'] !== '') {
            $q->where('completed', (int) $filtros['completed']);
        }

        if (!empty($filtros['busca'])) {
            $b = trim($filtros['busca']);
            $q->where(function ($qb) use ($b) {
                $qb->where('title', 'like', "%{$b}%")
                    ->orWhere('description', 'like', "%{$b}%");
            });
        }

        $p = $q->orderByDesc('created_at')->paginate($perPage, ['*'], 'page', $page);

        $items = [];
        foreach ($p->items() as $m) {
            /** @var TarefaModel $m */
            $items[] = $this->mapToListItemDTO($m);
        }

        return new PaginacaoDTO(
            items: $items,
            total: (int) $p->total(),
            page: (int) $p->currentPage(),
            perPage: (int) $p->perPage(),
            lastPage: (int) $p->lastPage()
        );
    }

    public function listar(): array
    {
        $rows = TarefaModel::query()
            ->orderBy('title')
            ->get();

        $out = [];
        foreach ($rows as $m) {
            $out[] = $this->mapToListItemDTO($m);
        }
        return $out;
    }

    public function carregar(int $id): ?TarefaResponseDTO
    {
        $m = TarefaModel::query()->find($id);

        if (!$m) return null;
        return $m ? $this->mapToResponseDTO($m) : null;
    }

    private function mapToListItemDTO(TarefaModel $m): TarefaListItemDTO
    {
        return new TarefaListItemDTO(
            id: $m->id,
            title: $m->title,
            description: $m->description,
            completed: (bool) $m->completed,
            created_at: $m->created_at,
            updated_at: $m->updated_at,
        );
    }

    private function mapToResponseDTO(TarefaModel $m): TarefaResponseDTO
    {
        return new TarefaResponseDTO(
            id: $m->id,
            title: $m->title,
            description: $m->description,
            completed: (bool) $m->completed,
            created_at: $m->created_at,
            updated_at: $m->updated_at,
        );
    }
}
