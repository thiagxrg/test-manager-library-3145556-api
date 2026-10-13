<?php

namespace Database\Seeders;

use App\Domain\Entities\User as DomainUser;
use App\Infrastructure\Persistence\Eloquent\UserModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Creates the initial admin from env credentials (D-10): the database cannot
     * produce the password hash, so the admin is never seeded by a migration.
     */
    public function run(): void
    {
        $username = DomainUser::normalizeUsername((string) env('ADMIN_USERNAME', 'admin'));

        if (UserModel::where('username', $username)->exists()) {
            return;
        }

        UserModel::create([
            'id' => (string) Str::uuid(),
            'username' => $username,
            'password_hash' => Hash::make((string) env('ADMIN_PASSWORD')),
            'role' => DomainUser::ROLE_ADMIN,
        ]);
    }
}
