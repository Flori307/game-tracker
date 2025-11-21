<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class ApiTokenAuth
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken() ?? $request->get('api_token');
        
        if (!$token) {
            return response()->json([
                'message' => 'Unauthorized',
                'error' => 'Token not provided'
            ], 401);
        }

        $user = User::where('api_token', $token)->first();
        
        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized', 
                'error' => 'Invalid token'
            ], 401);
        }


        auth()->setUser($user);
        
        return $next($request);
    }
}