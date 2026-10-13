<?php

namespace Library\Domain;

final class Category
{
    public function __construct(
        private string $id,
        private string $name,
    ) {
    }

    public function id(): string
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }
}
