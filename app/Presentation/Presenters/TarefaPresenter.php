<?php

namespace App\Presentation\Presenters;

use App\Application\DTOs\Tarefa\TarefaResponseDTO;
use App\Application\DTOs\Tarefa\TarefaListItemDTO;
use Carbon\Carbon;

final class TarefaPresenter
{
    public static function detalhe(TarefaResponseDTO $dto): array
    {
        return [
            'id' => $dto->id,
            'title' => $dto->title,
            'description' => $dto->description,
            'completed' => (bool) $dto->completed,
            'completed_desc' => self::statusDesc($dto->completed),
            'created_at' => Carbon::parse($dto->created_at)->format('d/m/Y H:i'),
            'updated_at' => Carbon::parse($dto->updated_at)->format('d/m/Y H:i'),
        ];
    }

    public static function lista(TarefaListItemDTO $dto): array
    {
        return [
            'id'     => $dto->id,
            'title'  => $dto->title,
            'description'  => $dto->description,
            'completed' => (bool) $dto->completed,
            'completed_desc' => self::statusDesc($dto->completed),
            'created_at' => Carbon::parse($dto->created_at)->format('d/m/Y H:i'),
            'updated_at' => Carbon::parse($dto->updated_at)->format('d/m/Y H:i'),
        ];
    }

    /** @param TarefaListItemDTO[] $items */
    public static function colecao(array $items): array
    {
        return array_map(fn(TarefaListItemDTO $i) => self::lista($i), $items);
    }

    private static function statusDesc(bool $completed): string
    {
        return $completed ? 'Sim' : 'Não';
    }
}
