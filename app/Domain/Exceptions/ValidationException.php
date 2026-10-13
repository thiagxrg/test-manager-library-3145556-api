<?php

namespace App\Domain\Exceptions;

class ValidationException extends DomainException
{
    /** @var array<int, array{field: string, issue: string}> */
    private array $errors;

    /** @param array<int, array{field: string, issue: string}> $errors */
    public function __construct(string $message, array $errors = [])
    {
        $this->errors = $errors;
        parent::__construct($message);
    }

    public function code(): string
    {
        return 'VALIDATION_ERROR';
    }

    public function statusCode(): int
    {
        return 422;
    }

    public function details(): array
    {
        return $this->errors;
    }
}
