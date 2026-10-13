<?php

namespace App\Domain\ValueObjects;

use DateTimeInterface;
use InvalidArgumentException;

final class DateRange
{
    private DateTimeInterface $start;
    private DateTimeInterface $end;

    public function __construct(DateTimeInterface $start, DateTimeInterface $end)
    {
        if ($end < $start) {
            throw new InvalidArgumentException('El fin del rango no puede ser anterior al inicio.');
        }

        $this->start = $start;
        $this->end = $end;
    }

    public function start(): DateTimeInterface
    {
        return $this->start;
    }

    public function end(): DateTimeInterface
    {
        return $this->end;
    }
}
