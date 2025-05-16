<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        // If user is not authenticated or doesn't have the required role
        if (!$user || !in_array($user->role, $roles)) {
            // Redirect based on user's authentication status
            return $user
                ? redirect()->route('home')->with('error', 'Unauthorized access!')
                : redirect()->route('login');
        }

        return $next($request);
    }
}
