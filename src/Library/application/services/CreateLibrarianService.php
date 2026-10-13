<?php

namespace Library\Application\Services;

use Library\Application\Ports\Outbound\PasswordHasher;
use Library\Application\Ports\Outbound\UserRepository;
use Library\Domain\Exceptions\UsernameTakenException;
use Library\Domain\User;

/** HU-02: only ever creates role=librarian. The admin role is never granted through this service. */
class CreateLibrarianService
{
    public function __construct(
        private UserRepository $users,
        private PasswordHasher $hasher,
    ) {
    }

    public function handle(string $username, string $password): User
    {
        $normalized = User::normalizeUsername($username);

        if ($this->users->existsByUsername($normalized)) {
            throw new UsernameTakenException();
        }

        $user = new User(
            $this->users->nextIdentity(),
            $normalized,
            $this->hasher->hash($password),
            User::ROLE_LIBRARIAN,
        );

        $this->users->save($user);

        return $user;
    }
}
