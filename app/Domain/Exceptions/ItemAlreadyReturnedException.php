<?php

namespace App\Domain\Exceptions;

class ItemAlreadyReturnedException extends DomainException
{
    public function __construct()
    {
        parent::__construct('This loan item has already been returned.');
    }

    public function code(): string
    {
        return 'ITEM_ALREADY_RETURNED';
    }

    public function statusCode(): int
    {
        return 409;
    }
}
