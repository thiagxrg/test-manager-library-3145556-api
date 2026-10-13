<?php

namespace App\Domain\Entities;

use App\Domain\Exceptions\ValidationException;

final class User
{
    public const ROLE_ADMIN = 'admin';
    public const ROLE_LIBRARIAN = 'librarian';

    private const VALID_ROLES = [self::ROLE_ADMIN, self::ROLE_LIBRARIAN];

    private string $username;

    public function __construct(
        private string $id,
        string $username,
        private string $passwordHash,
        private string $role,
    ) {
        if (! in_array($role, self::VALID_ROLES, true)) {
            throw new ValidationException("Invalid role: {$role}.");
        }

        $this->username = self::normalizeUsername($username);
    }

    public static function normalizeUsername(string $username): string
    {
        return mb_strtolower(trim($username));
    }

    public function id(): string
    {
        return $this->id;
    }

    public function username(): string
    {
        return $this->username;
    }

    public function passwordHash(): string
    {
        return $this->passwordHash;
    }

    public function role(): string
    {
        return $this->role;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }
}
