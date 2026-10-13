<?php

namespace Library\Domain\Exceptions;

class IsbnTakenException extends DomainException
{
    public function __construct()
    {
        parent::__construct('An active book with that ISBN already exists.');
    }

    public function code(): string
    {
        return 'ISBN_TAKEN';
    }

    public function statusCode(): int
    {
        return 409;
    }
}
