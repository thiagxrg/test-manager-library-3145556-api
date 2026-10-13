<?php

namespace Library\Application\Services;

use DateTimeImmutable;
use Library\Application\Ports\Outbound\ReportReader;
use Library\Domain\Exceptions\InvalidDateRangeException;

/** HU-10: a single aggregated query, grouped by frozen values (ADR-004). Admin only — enforced by the controller. */
class MostBorrowedReportService
{
    public function __construct(private ReportReader $reports)
    {
    }

    public function handle(DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        if ($to < $from) {
            throw new InvalidDateRangeException();
        }

        return $this->reports->mostBorrowed($from, $to);
    }
}
