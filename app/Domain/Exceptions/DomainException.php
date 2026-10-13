<?php

namespace App\Domain\Exceptions;

abstract class DomainException extends \RuntimeException
{
    abstract public function code(): string;

    abstract public function statusCode(): int;

    /** @return array<int, array{field: string, issue: string}> */
    public function details(): array
    {
        return [];
    }
}
