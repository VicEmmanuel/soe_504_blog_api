<?php

namespace App\Http\Middleware;

use App\Traits\HttpResponses;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    use HttpResponses;

    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Parse the token and authenticate the user
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException $e) {
            // Token has expired
//            return response()->json(['error' => 'Token expired'], 401);
            return $this->error([], 'Unauthorized', 403);

        } catch (TokenInvalidException $e) {
            // Token is invalid
//            return response()->json(['error' => 'Token invalid'], 401);
            return $this->error([], 'Unauthorized', 403);

        } catch (JWTException $e) {
            // Token is not provided or there's another issue
//            return response()->json(['error' => 'Token absent'], 401);
            return $this->error([], 'Unauthorized', 403);

        }
        return $next($request);
    }
}
