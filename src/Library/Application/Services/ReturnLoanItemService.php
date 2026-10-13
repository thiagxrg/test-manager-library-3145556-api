<?php

namespace Library\Application\Services;

use Library\Application\Ports\Outbound\BookRepository;
use Library\Application\Ports\Outbound\Clock;
use Library\Application\Ports\Outbound\LoanRepository;
use Library\Application\Ports\Outbound\TransactionManager;
use Library\Domain\Exceptions\NotFoundException;
use Library\Domain\LoanItem;

class ReturnLoanItemService
{
    public function __construct(
        private LoanRepository $loans,
        private BookRepository $books,
        private Clock $clock,
        private TransactionManager $transactions,
    ) {
    }

    public function handle(string $loanId, string $itemId): LoanItem
    {
        return $this->transactions->run(function () use ($loanId, $itemId) {
            $loan = $this->loans->findByIdForUpdate($loanId) ?? throw new NotFoundException('Loan not found.');

            $item = null;
            foreach ($loan->items() as $candidate) {
                if ($candidate->id() === $itemId) {
                    $item = $candidate;
                    break;
                }
            }

            if ($item === null) {
                throw new NotFoundException('Loan item not found.');
            }

            // Throws ItemAlreadyReturnedException if already returned (CA-07.2) — never increments twice.
            $item->returnItem($this->clock->now());

            $this->loans->updateReturnedAt($loanId, $itemId, $item->returnedAt());

            $book = $this->books->findById($item->bookId());
            if ($book !== null) {
                $book->restock();
                $this->books->save($book);
            }

            return $item;
        });
    }
}
