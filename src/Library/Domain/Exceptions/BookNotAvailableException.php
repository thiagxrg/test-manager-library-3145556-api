<?php

namespace Library\Domain\Exceptions;

class BookNotAvailableException extends DomainException
{
    public function __construct(string $bookTitle)
    {
        parent::__construct(sprintf('No copies of "%s" are available.', $bookTitle));
    }

    public function code(): string
    {
        return 'BOOK_NOT_AVAILABLE';
    }

    public function statusCode(): int
    {
        return 409;
    }
}
