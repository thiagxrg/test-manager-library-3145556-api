<?php

namespace Library\Adapters\Outbound\Persistence;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Eloquent/Sanctum adapter for the domain User entity. Sanctum's `auth:sanctum` guard requires
 * an Authenticatable Eloquent model — this is that adapter, kept in adapters/outbound, never used
 * directly by application services (they depend on Library\Domain\User instead).
 */
class UserModel extends Authenticatable
{
    use HasApiTokens, HasUuids;

    protected $table = 'user';

    public $timestamps = false;

    protected $fillable = [
        'id',
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
