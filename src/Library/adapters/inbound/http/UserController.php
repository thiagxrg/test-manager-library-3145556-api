<?php

namespace Library\Adapters\Inbound\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Library\Application\Services\CreateLibrarianService;
use Library\Domain\Exceptions\ValidationException;

class UserController
{
    public function __construct(private CreateLibrarianService $createLibrarian)
    {
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'string'],
        ]);

        // CA-02.3: requesting the admin role through the API responds 422.
        if ($data['role'] !== 'librarian') {
            throw new ValidationException(
                'Only the librarian role can be created through the API.',
                [['field' => 'role', 'issue' => 'must be "librarian"']],
            );
        }

        $user = $this->createLibrarian->handle($data['username'], $data['password']);

        return new JsonResponse([
            'id' => $user->id(),
            'username' => $user->username(),
            'role' => $user->role(),
        ], 201);
    }
}
