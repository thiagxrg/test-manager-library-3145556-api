<?php

namespace Library\Application\Ports\Outbound;

use DateTimeImmutable;
use Library\Domain\Loan;

interface LoanRepository
{
    public function nextIdentity(): string;

    public function nextItemIdentity(): string;

    /** Persists a brand-new loan with all its items in one transaction (CA-06.1..CA-06.7). */
    public function insert(Loan $loan): void;

    public function findById(string $id): ?Loan;

    /** Loads the loan with its items locked for update, for the return flow. */
    public function findByIdForUpdate(string $id): ?Loan;

    public function updateReturnedAt(string $loanId, string $itemId, DateTimeImmutable $returnedAt): void;

    /**
     * @param array{from?: DateTimeImmutable, to?: DateTimeImmutable, status?: string, member_id?: string} $filters
     * @return array{items: Loan[], total: int}
     */
    public function search(array $filters, int $page, int $size): array;

    /**
     * @return array<int, array{loan_id: string, item_id: string, book_title: string, member_name: string, due_at: DateTimeImmutable, days_late: int}>
     */
    public function findOverdueItems(DateTimeImmutable $now): array;
}
