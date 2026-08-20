<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware role ringkas ikut struktur database kita sendiri (table roles + users.role_id).
 * Guna: ->middleware('role:super_admin') / 'role:owner' / 'role:agent' / 'role:barber'
 * TAK perlukan package spatie/laravel-permission.
 */
class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isRole($role)) {
            abort(403, 'Anda tiada akses ke bahagian ini.');
        }

        return $next($request);
    }
}
