<?php

namespace App\Http\Middleware;

use App\ApiKey;

use Closure;

class AuthenticateClient
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->api_key == '') {
            return response("Invalid access key", 400);
        } else {
            $key = ApiKey::whereActive()->where('access_key', $request->api_key)->count();
            if ($key < 1) {
                return response("Invalid access key", 400);
            } else {
                return $next($request);
            }

        }
    }
}
