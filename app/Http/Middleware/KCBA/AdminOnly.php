<?php

namespace App\Http\Middleware\KCBA;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\KCBA\User;
use App\Models\KCBA\Member;

class AdminOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isAdmin = $this->isAdmin( $request->user() );
        if( $isAdmin ){
            return $next($request);
        }else{
            return new Response(status:401);
        }
    }

    public function isAdmin($user) {
        if( $user == null ) return false;
        
        $id = $user->id;
        $member = Member::where('user_id','=',$id)->get()->first();
        /** @var Member $member */
        return $member->isAdmin();
    }

}
