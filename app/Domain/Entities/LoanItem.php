<?php

namespace App\Domain\Entities;

use App\Domain\Exceptions\ItemAlreadyReturnedException;
use App\Domain\ValueObjects\Isbn;
use DateTimeImmutable;

final class LoanItem
{
    public function __construct(
        private string $id,
        private string $loanId,
        private string $bookId,
        private string $bookTitle,
        private Isbn $isbn,
        private string $categoryName,
        private DateTimeImmutable $dueAt,
        private ?DateTimeImmutable $returnedAt = null,
    ) {
    }

    public function id(): string
    {
        return $this->id;
    }

    public function loanId(): string
    {
        return $this->loanId;
    }

    public function bookId(): string
    {
        return $this->bookId;
    }

    public function bookTitle(): string
    {
        return $this->bookTitle;
    }

    public function isbn(): Isbn
    {
        return $this->isbn;
    }

    public function categoryName(): string
    {
        return $this->categoryName;
    }

    public function dueAt(): DateTimeImmutable
    {
        return $this->dueAt;
    }

    public function returnedAt(): ?DateTimeImmutable
    {
        return $this->returnedAt;
    }

    public function isReturned(): bool
    {
        return $this->returnedAt !== null;
    }

    public function isOverdue(DateTimeImmutable $now): bool
    {
        return ! $this->isReturned() && $this->dueAt < $now;
    }

    public function daysLate(DateTimeImmutable $now): int
    {
        if (! $this->isOverdue($now)) {
            return 0;
        }

        return $this->dueAt->diff($now)->days;
    }

    /** Returns the item. Throws ItemAlreadyReturnedException if already returned (CA-07.2). */
    public function returnItem(DateTimeImmutable $now): void
    {
        if ($this->isReturned()) {
            throw new ItemAlreadyReturnedException();
        }

        $this->returnedAt = $now;
    }
}
