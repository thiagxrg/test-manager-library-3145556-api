<?php

namespace App\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Eloquent/Sanctum adapter for the domain User entity. Sanctum's `auth:sanctum` guard requires
 * an Authenticatable Eloquent model — this is that adapter, kept in Infrastructure, never used
 * directly by Application services (they depend on App\Domain\Entities\User instead).
 */
class UserModel extends Authenticatable
{
    use HasApiTokens, HasUuids;

    protected $table = 'user';

    public $timestamps = false;

    protected $fillable = [
        'username',
        'password_hash',
        'role',
    ];

    protected $hidden = [
        'password_hash',
    ];

    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }
}
