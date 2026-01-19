<?php

namespace App\Application\DTOs\Paginacao;

final class PaginacaoDTO
{
    /** @param array<int, mixed> $items */
    public function __construct(
        public array $items,
        public int $total,
        public int $page,
        public int $perPage,
        public int $lastPage
    ) {}
}
