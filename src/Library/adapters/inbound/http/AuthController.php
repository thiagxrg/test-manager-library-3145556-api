<?php

namespace Library\Adapters\Inbound\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Library\Application\Services\LoginService;

class AuthController
{
    public function __construct(private LoginService $login)
    {
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        return new JsonResponse($this->login->handle($data['username'], $data['password']));
    }
}
