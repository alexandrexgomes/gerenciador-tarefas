<?php

namespace App\Infrastructure\Persistence\Eloquent\QueryRepositories;

use App\Application\Queries\PapelQueryRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Models\PapelModel;
use App\Application\DTOs\Papel\PapelListItemDTO;

final class PapelQueryRepository implements PapelQueryRepositoryInterface
{
    public function listar(): array
    {
        $rows = PapelModel::query()
            ->orderBy('nome')
            ->get();

        $out = [];
        foreach ($rows as $m) {
            $out[] = $this->toListItemDTO($m);
        }
        return $out;
    }

    private function toListItemDTO(PapelModel $m): PapelListItemDTO
    {
        return new PapelListItemDTO(
            id: $m->id,
            nome: $m->nome
        );
    }
}
