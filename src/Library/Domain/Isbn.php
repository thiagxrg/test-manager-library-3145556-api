<?php

namespace Library\Domain;

use InvalidArgumentException;

final class Isbn
{
    private string $value;

    public function __construct(string $value)
    {
        if (! preg_match('/^\d{13}$/', $value)) {
            throw new InvalidArgumentException('ISBN must be exactly 13 digits.');
        }

        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
