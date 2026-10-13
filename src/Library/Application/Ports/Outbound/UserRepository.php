<?php

namespace Library\Application\Ports\Outbound;

use Library\Domain\User;

interface UserRepository
{
    public function nextIdentity(): string;

    public function findByUsername(string $username): ?User;

    public function existsByUsername(string $username): bool;

    public function save(User $user): void;
}
