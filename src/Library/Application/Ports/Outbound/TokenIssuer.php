<?php

namespace Library\Application\Ports\Outbound;

use Library\Domain\User;

interface TokenIssuer
{
    /** @return array{access_token: string, token_type: string, expires_in: int} */
    public function issueFor(User $user): array;
}
