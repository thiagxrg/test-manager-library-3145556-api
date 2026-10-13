<?php

namespace App\Domain\Exceptions;

class InvalidCredentialsException extends DomainException
{
    public function __construct()
    {
        parent::__construct('Invalid username or password.');
    }

    public function code(): string
    {
        return 'INVALID_CREDENTIALS';
    }

    public function statusCode(): int
    {
        return 401;
    }
}
