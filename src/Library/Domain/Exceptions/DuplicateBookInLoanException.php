<?php

namespace Library\Domain\Exceptions;

class DuplicateBookInLoanException extends DomainException
{
    public function __construct()
    {
        parent::__construct('The same book cannot appear twice in a loan.');
    }

    public function code(): string
    {
        return 'DUPLICATE_BOOK_IN_LOAN';
    }

    public function statusCode(): int
    {
        return 422;
    }
}
