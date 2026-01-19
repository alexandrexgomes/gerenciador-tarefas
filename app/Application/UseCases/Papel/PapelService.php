<?php

namespace App\Application\UseCases\Papel;

use App\Application\Queries\PapelQueryRepositoryInterface;
use App\Application\DTOs\Papel\PapelListItemDTO;

final class PapelService
{
    public function __construct(
        private PapelQueryRepositoryInterface $queries
    ) {}

    /** @return PapelListItemDTO[] */
    public function listar(): array
    {
        return $this->queries->listar();
    }
}
