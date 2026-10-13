<?php

namespace App\Domain\ValueObjects;

use InvalidArgumentException;

final class Quantity
{
    private int $value;

    public function __construct(int $value)
    {
        if ($value < 1) {
            throw new InvalidArgumentException('La cantidad debe ser mayor o igual a 1.');
        }

        $this->value = $value;
    }

    public function value(): int
    {
        return $this->value;
    }
}
