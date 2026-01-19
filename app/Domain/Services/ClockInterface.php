<?php

namespace App\Domain\Services;

interface ClockInterface
{
    public function todayYmd(): string;
}