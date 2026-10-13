<?php

namespace Library\Domain\Exceptions;

class MemberHasOverdueException extends DomainException
{
    public function __construct()
    {
        parent::__construct('The member has overdue books and cannot borrow more until they are returned.');
    }

    public function code(): string
    {
        return 'MEMBER_HAS_OVERDUE';
    }

    public function statusCode(): int
    {
        return 409;
    }
}
