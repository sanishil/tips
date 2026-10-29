<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Keeps each portal to its own accounts: `role:admin` or `role:tech`.
 * Anyone else is sent to their own portal's dashboard.
 */
class EnsureUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $userRole = $request->user()->role;

        if ($userRole->value !== $role) {
            return redirect()->route($userRole->dashboardRoute());
        }

        return $next($request);
    }
}
