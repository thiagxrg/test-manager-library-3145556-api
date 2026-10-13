<?php

namespace Library\Domain\Exceptions;

class InvalidDateRangeException extends DomainException
{
    public function __construct()
    {
        parent::__construct('The range end date cannot be before the start date.');
    }

    public function code(): string
    {
        return 'INVALID_DATE_RANGE';
    }

    public function statusCode(): int
    {
        return 422;
    }
}
