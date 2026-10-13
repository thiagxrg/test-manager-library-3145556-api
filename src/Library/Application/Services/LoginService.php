<?php

namespace Library\Application\Services;

use Library\Application\Ports\Outbound\PasswordHasher;
use Library\Application\Ports\Outbound\TokenIssuer;
use Library\Application\Ports\Outbound\UserRepository;
use Library\Domain\Exceptions\InvalidCredentialsException;

/** HU-01: a single service, one use case (Article IV, SRP). */
class LoginService
{
    public function __construct(
        private UserRepository $users,
        private PasswordHasher $hasher,
        private TokenIssuer $tokens,
    ) {
    }

    /** @return array{access_token: string, token_type: string, expires_in: int, role: string} */
    public function handle(string $username, string $password): array
    {
        $user = $this->users->findByUsername($username);

        // CA-01.2: the same error whether the username or the password is wrong.
        if (! $user || ! $this->hasher->verify($password, $user->passwordHash())) {
            throw new InvalidCredentialsException();
        }

        $token = $this->tokens->issueFor($user);

        return $token + ['role' => $user->role()];
    }
}
