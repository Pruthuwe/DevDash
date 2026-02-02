<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $permission): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if user has admin type (bypass permission checks)
        if ($user->user_type === 'admin') {
            return $next($request);
        }

        // Check if user has a role and the role has the required permission
        if ($user->role && $user->role->permissions->contains('name', $permission)) {
            return $next($request);
        }

        // Permission denied
        abort(403, 'You do not have permission to access this resource.');
    }
}
