<?php

namespace Library\Adapters\Outbound\Persistence;

use Illuminate\Support\Str;
use Library\Application\Ports\Outbound\UserRepository;
use Library\Domain\User;

class EloquentUserRepository implements UserRepository
{
    public function nextIdentity(): string
    {
        return (string) Str::uuid();
    }

    public function findByUsername(string $username): ?User
    {
        $model = UserModel::where('username', User::normalizeUsername($username))->first();

        return $model ? $this->toDomain($model) : null;
    }

    public function existsByUsername(string $username): bool
    {
        return UserModel::where('username', User::normalizeUsername($username))->exists();
    }

    public function save(User $user): void
    {
        UserModel::updateOrCreate(
            ['id' => $user->id()],
            [
                'username' => $user->username(),
                'password_hash' => $user->passwordHash(),
                'role' => $user->role(),
            ],
        );
    }

    private function toDomain(UserModel $model): User
    {
        return new User($model->id, $model->username, $model->password_hash, $model->role);
    }
}
