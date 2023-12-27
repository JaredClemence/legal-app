<?php

namespace App\Http\Middleware\KCBA;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\KCBA\User;

class IsBarMember
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if( $request->user()->isSectionMember() ){
            return $next($request);
        }else{
            redirect('bar.login');
        }
    }
}
