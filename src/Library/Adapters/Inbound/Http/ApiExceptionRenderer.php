<?php

namespace Library\Adapters\Inbound\Http;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException as LaravelValidationException;
use InvalidArgumentException;
use Library\Domain\Exceptions\DomainException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

/**
 * Translates every exception into the single error body from api-contract.md §1
 * (code, message, details). Never lets a raw framework error reach the client
 * (constitution Article II / RNF-4) — no trace, no SQL, no server path, no password.
 */
final class ApiExceptionRenderer
{
    public static function render(Throwable $e): ?JsonResponse
    {
        if ($e instanceof DomainException) {
            return self::body($e->code(), $e->getMessage(), $e->details(), $e->statusCode());
        }

        if ($e instanceof InvalidArgumentException) {
            return self::body('VALIDATION_ERROR', $e->getMessage(), [], 422);
        }

        if ($e instanceof LaravelValidationException) {
            $details = [];
            foreach ($e->errors() as $field => $messages) {
                $details[] = ['field' => $field, 'issue' => $messages[0] ?? 'invalid'];
            }

            return self::body('VALIDATION_ERROR', 'One or more fields are invalid.', $details, 422);
        }

        if ($e instanceof AuthenticationException) {
            return self::body('INVALID_CREDENTIALS', 'Missing or invalid token.', [], 401);
        }

        if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
            return self::body('NOT_FOUND', 'The requested resource does not exist.', [], 404);
        }

        return null;
    }

    private static function body(string $code, string $message, array $details, int $status): JsonResponse
    {
        return new JsonResponse([
            'code' => $code,
            'message' => $message,
            'details' => $details,
        ], $status);
    }
}
