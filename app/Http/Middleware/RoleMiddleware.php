<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $rolesCsv)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        $roles = array_map('trim', explode('|', $rolesCsv));
        if (!in_array($user->role, $roles, true)) {
            return response()->json(['message' => 'Forbidden: role not allowed'], 403);
        }
        return $next($request);
    }
}