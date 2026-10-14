<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(401, 'Unauthenticated.');
        }

        $allowedRoles = [];

        foreach ($roles as $roleGroup) {
            foreach (explode(',', $roleGroup) as $role) {
                $cleanRole = trim($role);

                if ($cleanRole !== '') {
                    $allowedRoles[] = $cleanRole;
                }
            }
        }

        if ($user->role === 'admin' || in_array($user->role, $allowedRoles, true)) {
            return $next($request);
        }

        abort(403, 'You do not have permission to access this page.');
    }
}
