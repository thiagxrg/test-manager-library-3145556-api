<?php

namespace App\Domain\Exceptions;

class UsernameTakenException extends DomainException
{
    public function __construct()
    {
        parent::__construct('A user with that username already exists.');
    }

    public function code(): string
    {
        return 'USERNAME_TAKEN';
    }

    public function statusCode(): int
    {
        return 409;
    }
}
