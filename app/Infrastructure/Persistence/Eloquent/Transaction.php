<?php

namespace App\Infrastructure\Persistence\Eloquent;

use Illuminate\Support\Facades\DB;
use App\Domain\Services\TransactionManagerInterface;

final class Transaction implements TransactionManagerInterface
{
    public function executar(callable $callback)
    {
        return DB::transaction($callback);
    }
}
