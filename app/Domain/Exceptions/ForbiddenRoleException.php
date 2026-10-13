<?php

namespace App\Domain\Exceptions;

class ForbiddenRoleException extends DomainException
{
    public function __construct()
    {
        parent::__construct('You do not have permission to perform this operation.');
    }

    public function code(): string
    {
        return 'FORBIDDEN_ROLE';
    }

    public function statusCode(): int
    {
        return 403;
    }
}
