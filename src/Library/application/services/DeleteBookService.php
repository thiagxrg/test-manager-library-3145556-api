<?php

namespace Library\Application\Services;

use Library\Application\Ports\Outbound\BookRepository;
use Library\Application\Ports\Outbound\Clock;
use Library\Domain\Exceptions\BookHasOpenLoansException;
use Library\Domain\Exceptions\NotFoundException;

/** CA-03.5: soft delete only; a book with open loans is never deactivated. */
class DeleteBookService
{
    public function __construct(
        private BookRepository $books,
        private Clock $clock,
    ) {
    }

    public function handle(string $id): void
    {
        $book = $this->books->findActiveById($id) ?? throw new NotFoundException('Book not found.');

        if ($this->books->hasOpenLoans($id)) {
            throw new BookHasOpenLoansException();
        }

        $book->markDeleted($this->clock->now());

        $this->books->save($book);
    }
}
