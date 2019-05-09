<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;

class checkAdmin
{

    public function handle($request, Closure $next)
    {

        //get admin value of user from database
        $adminValue = JWTAuth::User()->isAdmin;

        if ($adminValue != 1) {
            return response()->json([
                'message' => 'Nemáte na toto povolenie.'
            ], 401);
        }

        return $next($request);
    }

}