<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Wpuszcza żądanie tylko wtedy, gdy zalogowany użytkownik ma jedną
     * z wymaganych ról. Administrator ma dostęp uniwersalny.
     *
     * Użycie w trasach: ->middleware('role:klient') lub 'role:pracownik,kierownik'.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(Response::HTTP_UNAUTHORIZED);
        }

        if ($user->isAdmin() || $user->hasAnyRole($roles)) {
            return $next($request);
        }

        abort(Response::HTTP_FORBIDDEN);
    }
}
