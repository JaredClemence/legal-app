<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAuthorizedToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!$request->isMethod('post')){
            $token = isset( $request->token ) ? $request->token : false;
            if( $token != env("SECURE_TOKEN")){
                abort(403, 'Unauthorized.');
            }
        }
        return $next($request);
    }
}
