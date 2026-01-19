<?php

namespace App\Application\Queries;

use App\Application\DTOs\Papel\PapelListItemDTO;

interface PapelQueryRepositoryInterface
{
    /** @return PapelListItemDTO[] */
    public function listar(): array;
}
