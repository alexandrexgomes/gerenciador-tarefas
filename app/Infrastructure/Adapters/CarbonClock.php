<?php

namespace App\Infrastructure\Adapters;

use App\Domain\Services\ClockInterface;
use Carbon\CarbonImmutable;

final class CarbonClock implements ClockInterface
{
    public function todayYmd(): string
    {
        return CarbonImmutable::now(config('app.timezone'))->format('Y-m-d');
    }
}