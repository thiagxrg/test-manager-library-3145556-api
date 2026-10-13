<?php

namespace Library\Adapters\Outbound\Security;

use Illuminate\Support\Carbon;
use Library\Adapters\Outbound\Persistence\UserModel;
use Library\Application\Ports\Outbound\TokenIssuer;
use Library\Domain\User;

class SanctumTokenIssuer implements TokenIssuer
{
    public function issueFor(User $user): array
    {
        $ttlMinutes = (int) env('JWT_TTL', 480);

        $userModel = UserModel::findOrFail($user->id());

        $token = $userModel->createToken(
            'api',
            ['*'],
            Carbon::now()->addMinutes($ttlMinutes),
        );

        return [
            'access_token' => $token->plainTextToken,
            'token_type' => 'Bearer',
            'expires_in' => $ttlMinutes * 60,
        ];
    }
}
