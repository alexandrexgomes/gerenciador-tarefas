<?php

namespace App\Domain\Exceptions;

use RuntimeException;

final class TarefaNaoEncontradaException extends RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Tarefa não encontrada");
    }
}
