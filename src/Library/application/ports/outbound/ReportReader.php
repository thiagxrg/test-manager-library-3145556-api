<?php

namespace Library\Application\Ports\Outbound;

use DateTimeImmutable;

interface ReportReader
{
    /**
     * One row per frozen book_id + book_title + category_name (ADR-004).
     *
     * @return array<int, array{book_id: string, book_title: string, category_name: string, times_borrowed: int}>
     */
    public function mostBorrowed(DateTimeImmutable $from, DateTimeImmutable $to): array;
}
