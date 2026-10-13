<?php

namespace Library\Adapters\Outbound\Security;

use Illuminate\Support\Facades\Hash;
use Library\Application\Ports\Outbound\PasswordHasher;

class BcryptPasswordHasher implements PasswordHasher
{
    public function hash(string $plainPassword): string
    {
        return Hash::make($plainPassword);
    }

    public function verify(string $plainPassword, string $hash): bool
    {
        return Hash::check($plainPassword, $hash);
    }
}
