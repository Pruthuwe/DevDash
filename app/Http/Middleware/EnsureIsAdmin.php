<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsAdmin
{
    /**
     * Settings (Roles & Users management) is intentionally restricted to
     * true admin accounts only — it is NOT delegable via the permissions
     * system, since a role that can grant/edit roles could otherwise
     * escalate its own access.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user()->user_type !== 'admin') {
            abort(403, 'Only administrators can access Settings.');
        }

        return $next($request);
    }
}
