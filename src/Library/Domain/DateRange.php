<?php

namespace Library\Domain;

use DateTimeInterface;
use InvalidArgumentException;

final class DateRange
{
    private DateTimeInterface $start;
    private DateTimeInterface $end;

    public function __construct(DateTimeInterface $start, DateTimeInterface $end)
    {
        if ($end < $start) {
            throw new InvalidArgumentException('The range end date cannot be before the start date.');
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
