<?php

namespace Library\Domain\Exceptions;

class NotFoundException extends DomainException
{
    public function __construct(string $message = 'The requested resource does not exist.')
    {
        parent::__construct($message);
    }

    public function code(): string
    {
        return 'NOT_FOUND';
    }

    public function statusCode(): int
    {
        return 404;
    }
}
