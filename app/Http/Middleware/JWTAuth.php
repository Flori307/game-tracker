<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class JWTAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        
        if (!$token) {
            // Проверяем токен в заголовке Authorization
            $authHeader = $request->header('Authorization');
            if ($authHeader && preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
                $token = $matches[1];
            }
        }

        if (!$token) {
            return response()->json([
                'message' => 'Token not provided',
                'error' => 'Authentication required'
            ], 401);
        }

        // Ищем пользователя по токену
        $user = User::where('api_token', $token)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Invalid token',
                'error' => 'Authentication failed'
            ], 401);
        }

        // Аутентифицируем пользователя
        auth()->setUser($user);

        return $next($request);
    }
}