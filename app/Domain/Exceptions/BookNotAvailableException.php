<?php

namespace App\Domain\Exceptions;

class BookNotAvailableException extends DomainException
{
    private string $bookTitle;

    public function __construct(string $bookTitle)
    {
        $this->bookTitle = $bookTitle;
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
