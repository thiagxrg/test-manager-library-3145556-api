<?php

namespace Library\Application\Services;

use Library\Application\Ports\Outbound\BookRepository;
use Library\Application\Ports\Outbound\CategoryRepository;
use Library\Application\Ports\Outbound\Clock;
use Library\Application\Ports\Outbound\LoanRepository;
use Library\Application\Ports\Outbound\MemberRepository;
use Library\Application\Ports\Outbound\TransactionManager;
use Library\Domain\Exceptions\DuplicateBookInLoanException;
use Library\Domain\Exceptions\MemberHasOverdueException;
use Library\Domain\Exceptions\MemberLimitExceededException;
use Library\Domain\Exceptions\NotFoundException;
use Library\Domain\Exceptions\ValidationException;
use Library\Domain\Loan;
use Library\Domain\LoanItem;

/** HU-06: one service, one use case. The withdraw-and-create-item step is a single transaction (ADR-002). */
class LendBooksService
{
    public function __construct(
        private LoanRepository $loans,
        private BookRepository $books,
        private MemberRepository $members,
        private CategoryRepository $categories,
        private Clock $clock,
        private TransactionManager $transactions,
    ) {
    }

    /** @param string[] $bookIds 1 to 3 ids */
    public function handle(string $memberId, array $bookIds, string $loanedByUserId): Loan
    {
        if (count($bookIds) < 1 || count($bookIds) > 3) {
            throw new ValidationException('A loan has between 1 and 3 items.');
        }

        if (count($bookIds) !== count(array_unique($bookIds))) {
            throw new DuplicateBookInLoanException();
        }

        $member = $this->members->findById($memberId) ?? throw new NotFoundException('Member not found.');

        $now = $this->clock->now();

        if ($this->members->hasOverdueForMember($member->id(), $now)) {
            throw new MemberHasOverdueException();
        }

        $currentCount = $this->members->countUnreturnedForMember($member->id());

        if ($currentCount + count($bookIds) > 3) {
            throw new MemberLimitExceededException($currentCount);
        }

        return $this->transactions->run(function () use ($bookIds, $member, $loanedByUserId, $now) {
            $books = $this->books->findByIdsForUpdate($bookIds);
            $booksById = [];
            foreach ($books as $book) {
                $booksById[$book->id()] = $book;
            }

            $loanId = $this->loans->nextIdentity();
            $items = [];

            foreach ($bookIds as $bookId) {
                $book = $booksById[$bookId] ?? throw new NotFoundException('Book not found.');

                $book->withdraw();
                $this->books->save($book);

                $category = $this->categories->findById($book->categoryId());

                $items[] = new LoanItem(
                    $this->loans->nextItemIdentity(),
                    $loanId,
                    $book->id(),
                    $book->title(),
                    $book->isbn(),
                    $category?->name() ?? '',
                    Loan::dueAtFor($now),
                );
            }

            $loan = new Loan($loanId, $member->id(), $loanedByUserId, $now, $items);

            $this->loans->insert($loan);

            return $loan;
        });
    }
}
