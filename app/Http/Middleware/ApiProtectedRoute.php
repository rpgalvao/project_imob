<?php

namespace LaraDev\Http\Middleware;

use Closure;
use Exception;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;


class ApiProtectedRoute
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof TokenInvalidException) {
                return response()->json(['status' => 'Forbbiden! Invalid token']);
            } elseif ($e instanceof TokenExpiredException) {
                return response()->json(['status' => 'Forbbiden! Expired token']);
            } else {
                return response()->json(['status' => 'Authorization token not found']);
            }
        }

        return $next($request);
    }
}
