<?php

namespace Library\Domain;

use DateTimeImmutable;
use Library\Domain\Exceptions\BookNotAvailableException;
use Library\Domain\Exceptions\ValidationException;

final class Book
{
    public function __construct(
        private string $id,
        private string $title,
        private string $author,
        private Isbn $isbn,
        private string $categoryId,
        private Quantity $totalCopies,
        private int $availableCopies,
        private ?string $coverKey = null,
        private ?DateTimeImmutable $deletedAt = null,
    ) {
        if ($availableCopies < 0 || $availableCopies > $totalCopies->value()) {
            throw new ValidationException('Available copies must be between 0 and the total.');
        }
    }

    public function id(): string
    {
        return $this->id;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function author(): string
    {
        return $this->author;
    }

    public function isbn(): Isbn
    {
        return $this->isbn;
    }

    public function categoryId(): string
    {
        return $this->categoryId;
    }

    public function totalCopies(): int
    {
        return $this->totalCopies->value();
    }

    public function availableCopies(): int
    {
        return $this->availableCopies;
    }

    public function coverKey(): ?string
    {
        return $this->coverKey;
    }

    public function isActive(): bool
    {
        return $this->deletedAt === null;
    }

    public function deletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function rename(string $title, string $author, string $categoryId): void
    {
        $this->title = $title;
        $this->author = $author;
        $this->categoryId = $categoryId;
    }

    /** Changes the total and adjusts available copies by the same difference (CA-03.3). Never leaves available < 0. */
    public function changeTotalCopies(Quantity $newTotal): void
    {
        $diff = $newTotal->value() - $this->totalCopies->value();
        $newAvailable = $this->availableCopies + $diff;

        if ($newAvailable < 0) {
            throw new ValidationException('The new total would leave available copies negative.');
        }

        $this->totalCopies = $newTotal;
        $this->availableCopies = $newAvailable;
    }

    /** Withdraws one copy. Throws BookNotAvailableException if none are left (CA-06.3). */
    public function withdraw(): void
    {
        if ($this->availableCopies <= 0) {
            throw new BookNotAvailableException($this->title);
        }

        $this->availableCopies--;
    }

    /** Restocks one returned copy. Never exceeds the total (CA-07.3). */
    public function restock(): void
    {
        if ($this->availableCopies < $this->totalCopies->value()) {
            $this->availableCopies++;
        }
    }

    public function setCoverKey(?string $coverKey): void
    {
        $this->coverKey = $coverKey;
    }

    public function markDeleted(DateTimeImmutable $now): void
    {
        $this->deletedAt = $now;
    }
}
