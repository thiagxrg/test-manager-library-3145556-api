<?php

namespace App\Domain\ValueObjects;

use InvalidArgumentException;

final class Isbn
{
    private string $value;

    public function __construct(string $value)
    {
        if (! preg_match('/^\d{13}$/', $value)) {
            throw new InvalidArgumentException('El ISBN debe tener exactamente 13 dígitos.');
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
