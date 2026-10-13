<?php

namespace App\Domain\Exceptions;

class BookHasOpenLoansException extends DomainException
{
    public function __construct()
    {
        parent::__construct('The book has unreturned loans and cannot be deactivated.');
    }

    public function code(): string
    {
        return 'BOOK_HAS_OPEN_LOANS';
    }

    public function statusCode(): int
    {
        return 409;
    }
}
