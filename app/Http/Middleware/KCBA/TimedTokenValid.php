<?php

namespace App\Http\Middleware\KCBA;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\KCBA\Member as BarMember;
use App\Models\KCBA\TimedSecurityToken;

class TimedTokenValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = TimedSecurityToken::where('hash','=',$request->input('token',''))->get()->first();
        if( $token === null ){
            //admins don't need tokens
            $member = BarMember::where('user_id','=',$request->user()?->id)->get()->first();
            if( $member === null || ($member !== null && $member?->isAdmin()==false) ){
                return response('Failed Request. Security Token is Required.', 401);
            }
        } else if( $token->isExpired() ){
            return response('Failed Request. Security Token is Expired.', 403);
        }
        return $next($request);
    }
}
