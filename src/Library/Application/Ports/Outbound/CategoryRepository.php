<?php

namespace Library\Application\Ports\Outbound;

use Library\Domain\Category;

interface CategoryRepository
{
    /** @return Category[] ordered by name */
    public function all(): array;

    public function findById(string $id): ?Category;
}
