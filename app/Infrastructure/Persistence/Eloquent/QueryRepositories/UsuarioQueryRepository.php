<?php

namespace App\Infrastructure\Persistence\Eloquent\QueryRepositories;

use App\Application\Queries\UsuarioQueryRepositoryInterface;
use App\Application\DTOs\Usuario\UsuarioListItemDTO;
use App\Infrastructure\Persistence\Eloquent\Models\UsuarioModel;
use App\Domain\Enums\StatusUsuario;
use App\Domain\Entities\Usuario;
use App\Application\DTOs\Papel\PapelListItemDTO;
use App\Application\DTOs\Paginacao\PaginacaoDTO;

final class UsuarioQueryRepository implements UsuarioQueryRepositoryInterface
{
    public function listar(): array
    {
        $rows = UsuarioModel::query()
            ->with(['papeis:id,nome'])
            ->orderBy('nome')
            ->get();

        $out = [];
        foreach ($rows as $m) {
            $out[] = $this->toListItemDTO($m);
        }
        return $out;
    }

    public function paginate(array $filtros, int $page, int $perPage): PaginacaoDTO
    {
        $q = UsuarioModel::query()
            ->with(['papeis:id,nome']);

        if (isset($filtros['status']) && $filtros['status'] !== '') {
            $q->where('status', (int) $filtros['status']);
        }

        if (!empty($filtros['busca'])) {
            $b = trim($filtros['busca']);
            $q->where(function ($qb) use ($b) {
                $qb->where('nome', 'like', "%{$b}%")
                    ->orWhere('email', 'like', "%{$b}%");
            });
        }

        $p = $q->orderBy('nome')->paginate($perPage, ['*'], 'page', $page);

        $items = [];
        foreach ($p->items() as $m) {
            /** @var UsuarioModel $m */
            $items[] = $this->toListItemDTO($m);
        }

        return new PaginacaoDTO(
            items: $items,
            total: (int) $p->total(),
            page: (int) $p->currentPage(),
            perPage: (int) $p->perPage(),
            lastPage: (int) $p->lastPage()
        );
    }

    public function buscarPorId(int $id): ?UsuarioListItemDTO
    {
        $m = UsuarioModel::query()
            ->with(['papeis:id,nome'])
            ->find($id);

        if (!$m) return null;
        return $m ? $this->toListItemDTO($m) : null;
    }

    private function toListItemDTO(UsuarioModel $m): UsuarioListItemDTO
    {
        $papeis = [];
        if ($m->relationLoaded('papeis')) {
            foreach ($m->papeis as $p) {
                $papeis[] = new PapelListItemDTO((int)$p->id, (string)$p->nome);
            }
        }
        return new UsuarioListItemDTO(
            id: $m->id,
            nome: $m->nome,
            email: $m->email,
            status: $m->status,
            papeis: $papeis
        );
    }

    public function carregarPapeisUsuario(int $usuarioId): array
    {
        $m = UsuarioModel::query()
            ->with(['papeis:id,nome'])
            ->find($usuarioId);

        if (!$m) return [];

        $papeis = [];
        foreach ($m->papeis as $p) {
            $papeis[] = new PapelListItemDTO(
                id: (int) $p->id,
                nome: (string) $p->nome
            );
        }

        return $papeis;
    }
}
