<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    private function normalizeRole(?string $role): string
    {
        return match (strtolower($role ?? '')) {
            'directure', 'director' => 'directur',
            default => strtolower($role ?? ''),
        };
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! auth()->check()) {
            return redirect()->route('users.member');
        }

        $currentRole = $this->normalizeRole(auth()->user()->role ?? '');
        $allowedRoles = array_map(fn ($role) => $this->normalizeRole($role), $roles);

        if (! in_array($currentRole, $allowedRoles, true)) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
