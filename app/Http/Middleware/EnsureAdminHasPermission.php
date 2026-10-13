<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Usage: ->middleware('admin.can:orders')
 * "admins" (managing admin accounts) is reserved for super admins.
 */
class EnsureAdminHasPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $admin = $request->user('admin');

        $allowed = $permission === 'admins'
            ? (bool) $admin?->is_super_admin
            : (bool) $admin?->hasPermission($permission);

        abort_unless($allowed, 403, 'You do not have permission to access this section.');

        return $next($request);
    }
}
