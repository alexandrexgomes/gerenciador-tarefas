<?php

namespace App\Domain\Services;

interface TransactionManagerInterface
{
    /**
     * Executa o callback dentro de uma transação (commit/rollback).
     *
     * @template T
     * @param callable():T $callback
     * @return T
     */
    public function executar(callable $callback);
}
