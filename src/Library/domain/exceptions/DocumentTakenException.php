<?php

namespace Library\Domain\Exceptions;

class DocumentTakenException extends DomainException
{
    public function __construct()
    {
        parent::__construct('A member with that document number already exists.');
    }

    public function code(): string
    {
        return 'DOCUMENT_TAKEN';
    }

    public function statusCode(): int
    {
        return 409;
    }
}
