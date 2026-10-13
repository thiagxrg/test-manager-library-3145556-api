<?php

namespace Library\Application\Ports\Outbound;

use Library\Domain\Book;

interface BookRepository
{
    public function nextIdentity(): string;

    /** Active books only; null if not found or deactivated (CA-04.3). */
    public function findActiveById(string $id): ?Book;

    /** Regardless of active state — used after a loan exists (history must still resolve). */
    public function findById(string $id): ?Book;

    /** @return array{items: Book[], total: int} */
    public function search(?string $query, ?string $categoryId, int $page, int $size): array;

    public function existsActiveIsbn(string $isbn, ?string $excludeBookId = null): bool;

    public function hasOpenLoans(string $bookId): bool;

    public function save(Book $book): void;

    /** Locks the rows for update inside the caller's transaction (ADR-002). @return Book[] */
    public function findByIdsForUpdate(array $ids): array;
}
