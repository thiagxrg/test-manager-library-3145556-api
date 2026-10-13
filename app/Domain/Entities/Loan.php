<?php

namespace App\Domain\Entities;

use App\Domain\Exceptions\DuplicateBookInLoanException;
use App\Domain\Exceptions\ValidationException;
use DateTimeImmutable;

final class Loan
{
    public const LOAN_PERIOD_DAYS = 14;

    /** @var LoanItem[] */
    private array $items;

    /** @param LoanItem[] $items */
    public function __construct(
        private string $id,
        private string $memberId,
        private string $loanedByUserId,
        private DateTimeImmutable $loanedAt,
        array $items,
    ) {
        if (count($items) < 1 || count($items) > 3) {
            throw new ValidationException('A loan has between 1 and 3 items.');
        }

        $bookIds = array_map(fn (LoanItem $item) => $item->bookId(), $items);
        if (count($bookIds) !== count(array_unique($bookIds))) {
            throw new DuplicateBookInLoanException();
        }

        $this->items = $items;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function memberId(): string
    {
        return $this->memberId;
    }

    public function loanedByUserId(): string
    {
        return $this->loanedByUserId;
    }

    public function loanedAt(): DateTimeImmutable
    {
        return $this->loanedAt;
    }

    /** @return LoanItem[] */
    public function items(): array
    {
        return $this->items;
    }

    public static function dueAtFor(DateTimeImmutable $loanedAt): DateTimeImmutable
    {
        return $loanedAt->modify('+'.self::LOAN_PERIOD_DAYS.' days');
    }

    /** open | overdue | closed — se calcula, nunca se guarda (Articulo VII). */
    public function status(DateTimeImmutable $now): string
    {
        $allReturned = true;
        $anyOverdue = false;

        foreach ($this->items as $item) {
            if (! $item->isReturned()) {
                $allReturned = false;
            }
            if ($item->isOverdue($now)) {
                $anyOverdue = true;
            }
        }

        if ($allReturned) {
            return 'closed';
        }

        return $anyOverdue ? 'overdue' : 'open';
    }
}
