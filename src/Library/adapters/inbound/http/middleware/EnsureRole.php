<?php

namespace Library\Adapters\Inbound\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Library\Domain\Exceptions\ForbiddenRoleException;

/** CA-01.3: insufficient role -> 403. A 403 never reveals whether the resource exists. */
class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role, $roles, true)) {
            throw new ForbiddenRoleException();
        }

        return $next($request);
    }
}
