<?php

namespace app\core;

use DateTimeImmutable;
use Psr\Clock\ClockInterface;

class Clock implements ClockInterface
{

    public function now(): DateTimeImmutable
    {
        return new DateTimeImmutable('now');
    }
}