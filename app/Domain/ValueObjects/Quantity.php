<?php

namespace App\Domain\ValueObjects;

use InvalidArgumentException;

final class Quantity
{
    private int $value;

    public function __construct(int $value)
    {
        if ($value < 1) {
            throw new InvalidArgumentException('Quantity must be greater than or equal to 1.');
        }

        $this->value = $value;
    }

    public function value(): int
    {
        return $this->value;
    }
}
